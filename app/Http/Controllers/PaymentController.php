<?php

namespace App\Http\Controllers;

use App\Events\PurchaseCompleted;
use App\Facades\SessionCart;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    private function weight(): int
    {
        return Cart::where('email', Auth::user()->email)->sum('weight');
    }

    private function delivery(): float
    {
        $delivery = Config::get('shipping.price')[Auth::user()->state] ?? 10;
        $weight = $this->weight();
        return $weight > 8000 ? $delivery + 8 : ($weight > 1500 ? $delivery + 6 : ($weight > 1000 ? $delivery + 3 : $delivery));
    }

    private function authenticatedLineTotal(Cart $cart): float
    {
        $product = Product::find($cart->product_id);
        $multiplier = $product && $product->isOwnedBy(Auth::user()) ? 0.85 : 1;
        return round((float) $product->price * $cart->quantity * $multiplier, 2);
    }

    private function subtotal(): float
    {
        return Cart::where('email', Auth::user()->email)->get()->sum(fn (Cart $cart) => $this->authenticatedLineTotal($cart));
    }

    private function total(): float
    {
        return round($this->subtotal() + $this->delivery(), 2);
    }

    public function createBill()
    {
        if (Auth::check()) {
            $user = User::findOrFail(Auth::id());
            if (! $user->state || ! $user->phone || ! $user->address) {
                session()->flash('message', 'Please complete delivery address');
                return redirect()->route('profile.show');
            }

            $cart = Cart::where('email', $user->email)->orderBy('created_at')->get();
            abort_if($cart->isEmpty(), 404);
            $delivery = $this->delivery();
            $subtotal = $this->subtotal();
            $total = $this->total();
            $state = $user->state;
            return view('cart.show', compact('cart', 'delivery', 'state', 'subtotal', 'total'));
        }

        $cart = SessionCart::instance('cart')->content();
        $details = session('delivery_info');
        if (! $details) {
            session()->flash('message', 'Please fill in delivery information');
            return redirect()->route('guest.checkout');
        }
        $state = $details['state'];
        $subtotal = SessionCart::instance('cart')->subtotal();
        $delivery = Config::get('shipping.price')[$state] ?? 10;
        $total = $subtotal + $delivery;
        return view('cart.show', compact('cart', 'delivery', 'state', 'subtotal', 'total', 'details'));
    }

    public function storeBill(Request $request)
    {
        $order = Auth::check() ? $this->completeAuthenticatedOrder() : $this->completeGuestOrder();
        session()->flash('message', 'Demo payment successful. Order '.$order->id.' is paid.');
        return Auth::check() ? redirect()->route('order.index') : redirect()->route('cart.index');
    }

    public function callback(Request $request) { return response()->json(['status' => 'demo-payment']); }
    public function redirect(Request $request) { return redirect()->route('billplz-create'); }

    private function completeAuthenticatedOrder(): Order
    {
        $user = Auth::user();
        $carts = Cart::where('email', $user->email)->orderBy('created_at')->get();
        abort_if($carts->isEmpty(), 404);

        return DB::transaction(function () use ($user, $carts) {
            $order = Order::create([
                'id' => (string) Str::uuid(), 'collection_id' => 'demo', 'email' => $user->email,
                'name' => $user->name, 'description' => 'Demo payment completed', 'delivery' => $this->delivery(),
                'status' => 1, 'amount' => $this->total(), 'paid' => 'true', 'paid_at' => Carbon::now(),
                'address' => $user->address, 'postcode' => $user->postcode, 'state' => $user->state, 'phone' => $user->phone,
            ]);

            foreach ($carts as $cart) {
                $product = Product::findOrFail($cart->product_id);
                abort_unless($product->status !== 2 && $product->canBeViewedBy($user), 404);
                $ownerPurchase = $product->isOwnedBy($user);
                $unitPrice = round((float) $product->price * ($ownerPurchase ? 0.85 : 1), 2);
                ProductOrder::create([
                    'id' => (string) Str::uuid(), 'billplz_id' => $order->id, 'product_id' => $product->id,
                    'title' => $product->title, 'price' => $unitPrice, 'quantity' => $cart->quantity,
                    'size' => $cart->size, 'color' => $cart->color, 'is_owner_purchase' => $ownerPurchase,
                ]);
                $this->recordSale($product, $cart->quantity, $ownerPurchase);
                $cart->delete();
            }
            event(new PurchaseCompleted($order));
            return $order;
        });
    }

    private function completeGuestOrder(): Order
    {
        $details = session('delivery_info');
        $contents = SessionCart::instance('cart')->content();
        $state = $details['state'];
        $delivery = Config::get('shipping.price')[$state] ?? 10;
        $subtotal = SessionCart::instance('cart')->subtotal();
        $order = Order::create([
            'id' => (string) Str::uuid(), 'collection_id' => 'demo', 'email' => $details['email'],
            'name' => $details['name'].' (G)', 'description' => 'Demo payment completed', 'delivery' => $delivery,
            'status' => 1, 'amount' => $subtotal + $delivery, 'paid' => 'true', 'paid_at' => Carbon::now(),
            'address' => $details['address'], 'postcode' => $details['postcode'], 'state' => $state, 'phone' => $details['phone'],
        ]);

        foreach ($contents as $cart) {
            $product = Product::findOrFail($cart->id);
            abort_unless($product->status !== 2 && $product->canBeViewedBy(null), 404);
            ProductOrder::create([
                'id' => (string) Str::uuid(), 'billplz_id' => $order->id, 'product_id' => $product->id,
                'title' => $product->title, 'price' => $product->price, 'quantity' => $cart->qty,
                'size' => $cart->options['size'], 'color' => $cart->options['color'], 'is_owner_purchase' => false,
            ]);
            $this->recordSale($product, $cart->qty, false);
        }
        SessionCart::instance('cart')->destroy();
        return $order;
    }

    private function recordSale(Product $product, int $quantity, bool $ownerPurchase): void
    {
        $product->increment('sold', $quantity);
        if ($ownerPurchase) {
            return;
        }

        $user = $product->owner;
        if (! $user) {
            return;
        }
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['id' => (string) Str::uuid(), 'name' => $user->name, 'commission' => 0, 'balance' => 0, 'status' => 1]
        );
        $income = round((float) $product->price * (float) $product->commission_rate * $quantity, 2);
        $oldBalance = $wallet->balance;
        $wallet->update(['commission' => $wallet->commission + $income, 'balance' => $wallet->balance + $income]);
        WalletTransaction::create([
            'user_id' => $user->id, 'wallet_id' => $wallet->id, 'balance' => $oldBalance,
            'income' => $income, 'new_balance' => $wallet->balance, 'status' => 3,
        ]);
    }
}
