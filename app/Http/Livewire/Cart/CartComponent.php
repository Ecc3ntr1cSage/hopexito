<?php

namespace App\Http\Livewire\Cart;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Facades\SessionCart;
use App\Models\Cart;
use App\Models\Product;

class CartComponent extends Component
{
    // increase cart quantity by 1
    public function increaseQuantity($rowId)
    {
        // Check if the user is authenticated
        $authCheck = Auth::check();
        // Get the cart instance based on user authentication
        $cart = $authCheck ? Cart::findOrFail($rowId) : SessionCart::instance('cart');
        // Get the current quantity
        $quantity = $authCheck ? $cart->quantity : $cart->get($rowId)->qty;
        // Increase the quantity
        $quantity++;
        // Update the cart
        if ($authCheck) {
            $subtotal = $cart->subtotal / $cart->quantity * $quantity;
            $weight = $cart->weight / $cart->quantity * $quantity;
            $cart->update(['quantity' => $quantity, 'subtotal' => $subtotal, 'weight' => $weight]);
        } else {
            $cart->update($rowId, $quantity);
        }
        return redirect()->back();
    }
    // decrease cart quantity by 1
    public function decreaseQuantity($rowId)
    {
        // Check if the user is authenticated
        $authCheck = Auth::check();
        // Get the cart instance based on user authentication
        $cart = $authCheck ? Cart::findOrFail($rowId) : SessionCart::instance('cart'); 
        // Get the current quantity
        $quantity = $authCheck ? $cart->quantity : $cart->get($rowId)->qty; 
        // Check if the quantity is greater than 1 before decreasing
        if ($quantity > 1) {
            // Decrease the quantity
            $quantity--;
            // Update the cart
            if ($authCheck) {
                $subtotal = $cart->subtotal / $cart->quantity * $quantity;
                $weight = $cart->weight / $cart->quantity * $quantity;
                $cart->update(['quantity' => $quantity, 'subtotal' => $subtotal, 'weight' => $weight]);
            } else {
                $cart->update($rowId, $quantity);
            }
        }
        else
        {
            $this->destroyCart($rowId);
        }
        return redirect()->back();
    }
    // remove cart from database
    public function destroyCart($rowId)
    {
        if (Auth::check()) {
            Cart::where('id', $rowId)->delete();
        } else {
            SessionCart::instance('cart')->remove($rowId);
        }
        return redirect()->back();
    }
    // return total amount of cart
    private function total()
    {
        $total = 0;
        if (Auth::check()) {
            foreach (Cart::where('email', Auth::user()->email)->get() as $cart) {
                $product = $cart->cartProduct;
                $multiplier = $product && $product->isOwnedBy(Auth::user()) ? 0.85 : 1;
                $total += (float) $product->price * $cart->quantity * $multiplier;
            }
            return $total;
        } else {
            return SessionCart::subtotal();
        }
    }

    public function render()
    {
        if (Auth::check()) {
            $cart = Cart::where('email', Auth::user()->email)->orderBy('created_at')->get();
            // The only discount is the server-calculated owner discount per line.
            $discount = 1;
            $products = Product::available()->where('status', 3)->inRandomOrder()->take(4)->get();
            $total = $this->total();
        } else {
            $cart = SessionCart::instance('cart')->content();
            $discount = 1;
            $products = Product::available()->where('status', 3)->inRandomOrder()->take(4)->get();
            $total = $this->total();
        }
        return view('livewire.cart.cart-component', compact('cart', 'total', 'discount', 'products'));
    }
}
