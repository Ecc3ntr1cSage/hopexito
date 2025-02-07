<?php

namespace App\Http\Controllers;

use App\Events\PurchaseCompleted;
use App\Events\UserHasCheckout;
use Gloudemans\Shoppingcart\Facades\Cart as SessionCart;
use App\Models\Cart;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class PaymentController extends Controller
{
    // return cart weight
    private function weight()
    {
        $weight = 0;
        foreach (Cart::where('email', Auth::user()->email)->get() as $cart) {
            $weight += $cart->weight;
        }
        return $weight;
    }
    // return delivery based on weight
    private function delivery()
    {
        $delivery = 0;
        $user = User::where('name', Auth::user()->name)->first();
        $state = $user['state'];
        $shipping = Config::get('shipping.price');
        $delivery = $shipping[$state];
        $weight = $this->weight();

        if ($weight > 1000 && $weight < 1500) {
            $delivery  = $delivery + 3;
        } elseif ($weight > 1500 && $weight < 8000) {
            $delivery  = $delivery + 6;
        } elseif ($weight > 8000) {
            $delivery = $delivery + 8;
        }
        return $delivery;
    }
    // return subtotal of cart
    private function subtotal()
    {
        $subtotal = 0;
        foreach (Cart::where('email', Auth::user()->email)->get() as $cart) {
            $subtotal += $cart->subtotal * $cart->discount;
        }
        return $subtotal;
    }
    // return total amount
    private function total()
    {
        return $total = $this->subtotal() + $this->delivery();
    }

    private function mixpanel()
    {
        $carts = Cart::where('email', Auth::user()->email)->orderBy('created_at')->get();
        foreach ($carts as $cart) {
            event(new UserHasCheckout($cart));
        }
    }

    public function createBill()
    {
        if (Auth::check()) {
            $user = User::findOrFail(Auth::user()->id);
            if ($user['state'] && $user['phone'] && $user['address']) {
                $state = $user['state'];
            } else {
                session()->flash('message', 'Please complete delivery address');
                return redirect()->route('profile.show');
            }
            $cart = Cart::where('email', Auth::user()->email)->orderBy('created_at')->get();
            $delivery = $this->delivery();
            $subtotal = $this->subtotal();
            $total = $this->total();
            return view('cart.show', compact('cart', 'delivery', 'state', 'subtotal', 'total'));
        } else {
            $cart = SessionCart::instance('cart')->content();
            $details = session('delivery_info');
            $state = $details['state'];
            $subtotal = SessionCart::instance('cart')->subtotal();
            $shipping = Config::get('shipping.price');
            $delivery = $shipping[$state];
            $total = $subtotal + $delivery;

            return view('cart.show', compact('cart', 'delivery', 'state', 'subtotal', 'total', 'details'));
        }
    }
    // prompt payment gateway
    public function storeBill(Request $request)
    {
        $request->validate([
            'radio' => 'required'
        ]);

        if (Auth::check()) {
            $billplz = array(
                'collection_id' => config('billplz.collection'),
                'email' => Auth::user()->email,
                'name' => Auth::user()->name,
                'mobile' => Auth::user()->phone,
                'description' => 'Thank you for supporting us!',
                'amount' => $this->total() * 100,
                'reference_1_label' => "Bank Code",
                'reference_1' => $request->input('radio'),
                'callback_url' => route('billplz-callback'),
                'redirect_url' => route('billplz-redirect'),
            );
        } else {
            $details = session('delivery_info');
            $state = $details['state'];
            $subtotal = SessionCart::instance('cart')->subtotal();
            $shipping = Config::get('shipping.price');
            $delivery = $shipping[$state];
            $total = $subtotal + $delivery;
            $billplz = array(
                'collection_id' => config('billplz.collection'),
                'email' => $details['email'],
                'name' => $details['name'],
                'mobile' => $details['phone'],
                'description' => 'Thank you for supporting us!',
                'amount' => $total * 100,
                'reference_1_label' => "Bank Code",
                'reference_1' => $request->input('radio'),
                'callback_url' => route('billplz-callback'),
                'redirect_url' => route('billplz-redirect'),
            );
        }

        //Live Production Key
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, 'https://www.billplz.com/api/v3/bills');
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $billplz);
        // curl_setopt($ch, CURLOPT_USERPWD, config('billplz.key') . ':' . 'S-MvzEa0nP4xJycUE84VVwqw');
        // curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // $result = curl_exec($ch);
        // $info = curl_getinfo($ch);
        // curl_close($ch);
        // $obj = json_decode($result);
        // $billId = $obj->id;

        // return redirect('https://www.billplz.com/bills/' . $billId . '?auto_submit=true');

        // Testing Production Key (Sandbox)

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.billplz-sandbox.com/api/v3/bills');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $billplz);
        curl_setopt($ch, CURLOPT_USERPWD, config('billplz.key') . ':' . 'S-F-eaovMEuHHoCv8nl8nJVA');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        $obj = json_decode($result);
        $billId = $obj->id;

        return redirect('https://www.billplz-sandbox.com/bills/' . $billId . '?auto_submit=true');
    }
    // Update Wallet
    private function updateCommissionGuest($cart)
    {
        try {
            $product = Product::findOrFail($cart->id);
            $user = User::where('name', $cart->options['shopname'])->first();
            $wallet = Wallet::where('user_id', $user->id)->first();
            $wallet_balance = $wallet->balance;
            $commission = $wallet->commission + $product->commission * $cart->qty;
            $balance = $wallet->balance + $product->commission * $cart->qty;
            $quantity = $product->sold + $cart->qty;

            $wallet->update([
                'commission' => $commission,
                'balance' => $balance,
            ]);

            WalletTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'balance' => $wallet_balance,
                'income' => $product->commission * $cart->qty,
                'new_balance' => $balance,
                'status' => 3
            ]);

            $product->update([
                'sold' => $quantity
            ]);
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }
    // Update Wallet
    private function updateCommission(Cart $cart)
    {
        try {
            $product = Product::findOrFail($cart->product_id);
            $user = User::where('name', $cart->shopname)->first();
            $wallet = Wallet::where('user_id', $user->id)->first();
            $wallet_balance = $wallet->balance;
            $commission = $wallet->commission + $product->commission * $cart->quantity;
            $balance = $wallet->balance + $product->commission * $cart->quantity;
            $quantity = $product->sold + $cart->quantity;

            $wallet->update([
                'commission' => $commission,
                'balance' => $balance,
            ]);

            WalletTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'balance' => $wallet_balance,
                'income' => $product->commission * $cart->quantity,
                'new_balance' => $balance,
                'status' => 3
            ]);

            $product->update([
                'sold' => $quantity
            ]);

        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }
    // Payment gateway redirect
    public function redirect(Request $request)
    {
        try {
            $bill = $request->all();
            $billplz = $bill['billplz'];
            if (Auth::check()) {
                $carts = Cart::where('email', Auth::user()->email)->orderBy('created_at')->get();
                $deleteCarts = Cart::where('email', Auth::user()->email)->orderBy('created_at')->get();
            }
            if ($billplz['paid'] == 'true') {
                // Store order from successful payments.
                if (Auth::check()) {
                    $order = Order::create([
                        'id' => $billplz['id'],
                        'collection_id' => config('billplz.collection'),
                        'email' => Auth::user()->email,
                        'name' => Auth::user()->name,
                        'description' => 'Thank you for supporting us!',
                        'delivery' => $this->delivery(),
                        'status' => 1,
                        'amount' => $this->total(),
                        'paid' => $billplz['paid'],
                        'paid_at' => Carbon::parse($billplz['paid_at']),
                        'address' => Auth::user()->address,
                        'postcode' => Auth::user()->postcode,
                        'state' => Auth::user()->state,
                        'phone' => Auth::user()->phone
                    ]);
                    event(new PurchaseCompleted($order));
                } else {
                    $details = session('delivery_info');
                    $state = $details['state'];
                    $subtotal = SessionCart::instance('cart')->subtotal();
                    $shipping = Config::get('shipping.price');
                    $delivery = $shipping[$state];
                    $total = $subtotal + $delivery;
                    $order = Order::create([
                        'id' => $billplz['id'],
                        'collection_id' => config('billplz.collection'),
                        'email' => $details['email'],
                        'name' => $details['name'] . ' (G)',
                        'description' => 'Thank you for supporting us!',
                        'delivery' => $delivery,
                        'status' => 1,
                        'amount' => $total,
                        'paid' => $billplz['paid'],
                        'paid_at' => Carb::parse($billplz['paid_at']),
                        'address' => $details['address'],
                        'postcode' => $details['postcode'],
                        'state' => $details['state'],
                        'phone' => $details['phone']
                    ]);
                }
                // Store products in the order
                if (Auth::check()) {
                    foreach ($carts as $cart) {
                        ProductOrder::create([
                            'id' => $cart->id,
                            'billplz_id' => $billplz['id'],
                            'product_id' => $cart->product_id,
                            'title' => $cart->title,
                            'price' => $cart->price,
                            'quantity' => $cart->quantity,
                            'size' => $cart->size,
                            'color' => $cart->color
                        ]);
                        $this->updateCommission($cart);
                    }
                } else {
                    foreach (SessionCart::instance('cart')->content() as $cart) {
                        ProductOrder::create([
                            'id' => $cart->rowId,
                            'billplz_id' => $billplz['id'],
                            'product_id' => $cart->id,
                            'title' => $cart->name,
                            'price' => $cart->price,
                            'quantity' => $cart->qty,
                            'size' => $cart->options['size'],
                            'color' => $cart->options['color'],
                        ]);
                        $this->updateCommissionGuest($cart);
                    }
                }
                //Remove Successful Cart
                if (Auth::check()) {
                    foreach ($deleteCarts as $deleteCart) {
                        Cart::where('id', $deleteCart->id)->delete();
                    }
                    session()->flash('message', 'Transaction Successful');
                    return redirect()->route('order.index');
                } else {
                    SessionCart::instance('cart')->destroy();
                    session()->flash('message', 'Transaction Successful');
                    return redirect()->route('cart.index');
                }
            } else {
                session()->flash('message', 'Transaction Unsuccessful');
                return redirect()->route('billplz-create');
            }
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }
}
