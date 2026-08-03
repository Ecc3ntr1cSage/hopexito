<?php

namespace App\Http\Controllers;

use App\Facades\SessionCart;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        return view('cart.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
            'size' => ['required', 'in:XS,S,M,L,XL,2XL'],
            'color' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::with('variants')->findOrFail($validated['product_id']);
        $variant = $product->variants->firstWhere('color', $validated['color']);
        abort_unless($variant, 422, 'That color is not available for this product.');
        abort_unless($product->status !== 2, 404);
        abort_unless($product->canBeViewedBy(Auth::user()), 404);

        $isOwner = $product->isOwnedBy(Auth::user());
        $price = (float) $product->price;
        $unitPrice = round($price * ($isOwner ? 0.85 : 1), 2);
        $cardImage = (int) $product->preview === 1
            ? ($variant->image_back_url ?: $variant->image_front_url)
            : $variant->image_front_url;
        $options = [
            'size' => $validated['size'],
            'color' => $validated['color'],
            'product_image' => $cardImage,
            'product_image_2' => $variant->image_back_url,
            'owner_purchase' => $isOwner,
        ];

        if (Auth::check()) {
            $cart = Cart::create([
                'id' => uniqid(),
                'product_id' => $product->id,
                'email' => Auth::user()->email,
                'title' => $product->title,
                'quantity' => $validated['quantity'],
                'price' => $unitPrice,
                'subtotal' => $unitPrice * $validated['quantity'],
                'weight' => 500 * $validated['quantity'],
                'size' => $validated['size'],
                'color' => $validated['color'],
            ]);
            session()->flash('message', 'Successfully added to cart');

            return $request->has('buy_now')
                ? redirect()->route('billplz-create')
                : redirect()->route('cart.index');
        }

        $cart = SessionCart::instance('cart')->add([
            'id' => $product->id,
            'name' => $product->title,
            'qty' => $validated['quantity'],
            'price' => $price,
            'weight' => 500 * $validated['quantity'],
            'options' => array_merge($options, ['shopname' => $product->shopname]),
        ]);

        session()->flash('message', $request->has('buy_now') ? 'Fill in delivery information' : 'Successfully added to cart');

        return $request->has('buy_now')
            ? redirect()->route('guest.checkout')
            : redirect()->route('cart.index');
    }
}
