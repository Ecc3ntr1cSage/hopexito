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
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    private function weight(): int
    {
        return Cart::where('email', Auth::user()->email)->sum('weight');
    }

    private function delivery(): float
    {
        $state = Auth::user()->state;
        $delivery = Config::get('shipping.price')[$state] ?? 10;
        $weight = $this->weight();

        if ($weight > 1000 && $weight < 1500) {
            return $delivery + 3;
        }

        if ($weight > 1500 && $weight < 8000) {
            return $delivery + 6;
        }

        if ($weight > 8000) {
            return $delivery + 8;
        }

        return $delivery;
    }

    private function subtotal(): float
    {
        return Cart::where('email', Auth::user()->email)->get()->sum(fn ($cart) => $cart->subtotal * ($cart->discount ?? 1));
    }

    private function total(): float
    {
        return $this->subtotal() + $this->delivery();
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

        return Auth::check()
            ? redirect()->route('order.index')
            : redirect()->route('cart.index');
    }

    public function callback(Request $request)
    {
        return response()->json(['status' => 'demo-payment']);
    }

    public function redirect(Request $request)
    {
        return redirect()->route('billplz-create');
    }

    private function completeAuthenticatedOrder(): Order
    {
        $user = Auth::user();
        $carts = Cart::where('email', $user->email)->orderBy('created_at')->get();

        $order = Order::create([
            'id' => (string) Str::uuid(),
            'collection_id' => 'demo',
            'email' => $user->email,
            'name' => $user->name,
            'description' => 'Demo payment completed',
            'delivery' => $this->delivery(),
            'status' => 1,
            'amount' => $this->total(),
            'paid' => 'true',
            'paid_at' => Carbon::now(),
            'address' => $user->address,
            'postcode' => $user->postcode,
            'state' => $user->state,
            'phone' => $user->phone,
        ]);

        foreach ($carts as $cart) {
            ProductOrder::create([
                'id' => (string) Str::uuid(),
                'billplz_id' => $order->id,
                'product_id' => $cart->product_id,
                'title' => $cart->title,
                'price' => $cart->price,
                'quantity' => $cart->quantity,
                'size' => $cart->size,
                'color' => $cart->color,
            ]);

            $this->updateCommission($cart);
            $cart->delete();
        }

        event(new PurchaseCompleted($order));

        return $order;
    }

    private function completeGuestOrder(): Order
    {
        $details = session('delivery_info');
        $state = $details['state'];
        $subtotal = SessionCart::instance('cart')->subtotal();
        $delivery = Config::get('shipping.price')[$state] ?? 10;
        $total = $subtotal + $delivery;

        $order = Order::create([
            'id' => (string) Str::uuid(),
            'collection_id' => 'demo',
            'email' => $details['email'],
            'name' => $details['name'].' (G)',
            'description' => 'Demo payment completed',
            'delivery' => $delivery,
            'status' => 1,
            'amount' => $total,
            'paid' => 'true',
            'paid_at' => Carbon::now(),
            'address' => $details['address'],
            'postcode' => $details['postcode'],
            'state' => $details['state'],
            'phone' => $details['phone'],
        ]);

        foreach (SessionCart::instance('cart')->content() as $cart) {
            ProductOrder::create([
                'id' => (string) Str::uuid(),
                'billplz_id' => $order->id,
                'product_id' => $cart->id,
                'title' => $cart->name,
                'price' => $cart->price,
                'quantity' => $cart->qty,
                'size' => $cart->options['size'],
                'color' => $cart->options['color'],
            ]);

            $this->updateCommissionGuest($cart);
        }

        SessionCart::instance('cart')->destroy();

        return $order;
    }

    private function updateCommission(Cart $cart): void
    {
        $product = Product::find($cart->product_id);
        $user = User::where('name', $cart->shopname)->first();

        if (! $product || ! $user) {
            return;
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['id' => (string) Str::uuid(), 'name' => $user->name, 'commission' => 0, 'balance' => 0, 'status' => 1]
        );

        $income = $product->commission * $cart->quantity;
        $oldBalance = $wallet->balance;

        $wallet->update([
            'commission' => $wallet->commission + $income,
            'balance' => $wallet->balance + $income,
        ]);

        WalletTransaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'balance' => $oldBalance,
            'income' => $income,
            'new_balance' => $wallet->balance,
            'status' => 3,
        ]);

        $product->increment('sold', $cart->quantity);
    }

    private function updateCommissionGuest($cart): void
    {
        $product = Product::find($cart->id);
        $user = $product ? User::where('name', $cart->options['shopname'])->first() : null;

        if (! $product || ! $user) {
            return;
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['id' => (string) Str::uuid(), 'name' => $user->name, 'commission' => 0, 'balance' => 0, 'status' => 1]
        );

        $income = $product->commission * $cart->qty;
        $oldBalance = $wallet->balance;

        $wallet->update([
            'commission' => $wallet->commission + $income,
            'balance' => $wallet->balance + $income,
        ]);

        WalletTransaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'balance' => $oldBalance,
            'income' => $income,
            'new_balance' => $wallet->balance,
            'status' => 3,
        ]);

        $product->increment('sold', $cart->qty);
    }
}
