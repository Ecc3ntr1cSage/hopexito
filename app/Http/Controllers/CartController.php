<?php

namespace App\Http\Controllers;

use App\Events\AddedToCart;
use App\Models\Product;
use App\Models\Cart;
use App\Facades\SessionCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        return view('cart.index');
    }

    public function create()
    {
        //
    }
    // store cart into database
    public function store(Request $request)
    {
        if ($request->has('add_to_cart')) {
            $request->validate([
                'size' => 'required',
                'color' => 'required',
            ]);
            $product = Product::findOrFail($request->input('product_id'));
            $rowId = uniqid(10);
            if (Auth::check()) {
                $cart = Cart::create([
                    'id' => $rowId,
                    'product_id' => $product->id,
                    'email' => Auth::user()->email,
                    'shopname' => $product->shopname,
                    'title' => $product->title,
                    'quantity' => $request->input('quantity'),
                    'price' => $product->price,
                    'subtotal' => $product->price * $request->input('quantity'),
                    'weight' => 500 * $request->input('quantity'),
                    'size' => $request->input('size'),
                    'color' => $request->input('color')
                ]);
                event(new AddedToCart($cart));
            } else {
                $cart = SessionCart::instance('cart')->add(['id' => $product->id, 'name' => $product->title, 'qty' => $request->input('quantity'), 'price' => $product->price, 'weight' => 500 * $request->input('quantity'), 'options' => ['size' => $request->input('size'), 'color' => $request->input('color'), 'shopname' => $product->shopname, 'product_image' => $product->product_image]]);
            }
            session()->flash('message', 'Successfully added to cart');
            return redirect()->route('cart.index', $product->slug);
        } 
        elseif ($request->has('buy_now')) {
            $request->validate([
                'size' => 'required',
                'color' => 'required',
            ]);
            $product = Product::findOrFail($request->input('product_id'));
            $cart = SessionCart::instance('cart')->add(['id' => $product->id, 'name' => $product->title, 'qty' => $request->input('quantity'), 'price' => $product->price, 'weight' => 500 * $request->input('quantity'), 'options' => ['size' => $request->input('size'), 'color' => $request->input('color'), 'shopname' => $product->shopname, 'product_image' => $product->product_image]]);
            session()->flash('message', 'Fill in delivery information');
            return redirect()->route('guest.checkout');
        }
    }

    public function show(Cart $cart)
    {
        //
    }

    public function edit(Cart $cart)
    {
        //
    }

    public function update(Cart $cart, $rowId)
    {
        // 
    }

    public function destroy($rowId)
    {
        // 
    }
}
