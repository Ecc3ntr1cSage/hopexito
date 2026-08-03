<?php

namespace App\Http\Controllers;

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
        abort_unless(Auth::check(), 403);

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

    public function storeBill(Request $request)
    {
        abort_unless(Auth::check(), 403);

        $paymentResult = $request->input('payment_result', 'success');
        abort_unless(in_array($paymentResult, ['success', 'failed'], true), 422);

        if ($paymentResult === 'failed') {
            session()->flash('message', 'Payment was not completed. Review your delivery details and try again.');

            return redirect()->route('guest.checkout');
        }

        $order = $this->completeAuthenticatedOrder();
        session()->put('last_order_id', $order->id);
        session()->flash('message', 'Demo payment successful. Order '.$order->id.' is paid.');

        return redirect()->route('order.index');
    }

    public function callback()
    {
        return response()->json(['status' => 'demo-payment']);
    }

    public function redirect()
    {
        return redirect()->route('billplz-create');
    }

    private function completeAuthenticatedOrder(): Order
    {
        $user = Auth::user();
        $carts = Cart::where('email', $user->email)->orderBy('created_at')->get();
        abort_if($carts->isEmpty(), 404);

        return DB::transaction(function () use ($user, $carts) {
            $order = Order::create([
                'id' => (string) Str::uuid(), 'email' => $user->email,
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
            return $order;
        });
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
