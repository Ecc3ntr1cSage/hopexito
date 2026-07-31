<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\MockupGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductsController extends Controller
{
    public function create()
    {
        return view('product.create', ['catalog' => config('catalog.types')]);
    }

    public function store(Request $request, MockupGenerator $mockups)
    {
        $validated = $request->validate([
            'product_type' => ['required', 'string', 'in:shirt,sweat,hoodie'],
            'title' => ['required', 'string', 'max:255'],
            'tags' => ['required', 'string', 'max:255'],
            'visibility' => ['nullable', 'in:public,private'],
            'image_front' => ['required', 'image', 'max:8192'],
            'image_back' => ['nullable', 'image', 'max:8192'],
            'preview_color' => ['nullable', 'string', 'in:White,Black,Gray'],
            'preview' => ['nullable', 'boolean'],
        ]);

        $catalog = config('catalog.types.'.$validated['product_type']);
        abort_unless($catalog, 422, 'Unknown product type.');

        $frontDesignPath = $request->file('image_front')->store('designs/front', 'public');
        $backDesignPath = $request->file('image_back')?->store('designs/back', 'public');

        DB::transaction(function () use ($request, $validated, $catalog, $mockups, $frontDesignPath, $backDesignPath) {
            $product = Product::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'slug' => Str::random(30),
                'product_type' => $validated['product_type'],
                'visibility' => $validated['visibility'] ?? 'public',
                'tags' => $validated['tags'],
                'price' => $catalog['price'],
                'commission_rate' => config('catalog.commission_rate'),
                'collection_id' => $request->input('collection_id'),
                'status' => 1,
                'sold' => 0,
                'preview' => (int) $request->input('preview', 0),
            ]);

            foreach (config('catalog.colors') as $color) {
                $frontPath = $mockups->generate(
                    $request->file('image_front'),
                    'mockups/'.strtolower($color).'-'.$validated['product_type'].'-front.png',
                    $catalog['front_position'],
                    $color,
                    'products/'.$product->id.'/'.$color.'-front.png'
                );

                $backPath = null;
                if ($request->hasFile('image_back')) {
                    $backPath = $mockups->generate(
                        $request->file('image_back'),
                        'mockups/'.strtolower($color).'-'.$validated['product_type'].'-back.png',
                        $catalog['back_position'],
                        $color,
                        'products/'.$product->id.'/'.$color.'-back.png'
                    );
                }

                ProductVariant::create([
                    'product_id' => $product->id,
                    'color' => $color,
                    'image_front_path' => $frontPath,
                    'image_back_path' => $backPath,
                ]);
            }
        });

        session()->flash('message', 'Product Created');
        return redirect()->route('product.manage');
    }

    public function show(Product $product)
    {
        abort_unless($product->canBeViewedBy(Auth::user()), 404);

        $product->load('variants', 'owner.profile');
        $user = $product->owner;
        $products = Product::available()
            ->where('user_id', $product->user_id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(8)
            ->get();
        $discover = Product::available()
            ->where('user_id', '!=', $product->user_id)
            ->inRandomOrder()
            ->take(8)
            ->get();
        $totalDesigns = Product::where('user_id', $product->user_id)->count();
        $colors = $product->variants->pluck('color')->values();
        $variantData = $product->variants->mapWithKeys(fn ($variant) => [
            $variant->color => ['front' => $variant->image_front_url, 'back' => $variant->image_back_url],
        ]);

        return view('product.show', compact('product', 'products', 'user', 'colors', 'totalDesigns', 'discover', 'variantData'));
    }

    public function edit(Product $product)
    {
        abort_unless($product->isOwnedBy(Auth::user()), 403);
        return redirect()->route('product.manage');
    }

    public function update(Request $request, Product $product)
    {
        abort_unless($product->isOwnedBy(Auth::user()), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'tags' => ['required', 'string', 'max:255'],
            'visibility' => ['required', 'in:public,private'],
        ]);

        $product->update($validated);
        return redirect()->route('product.manage')->with('message', 'Product Updated');
    }

    public function destroy(Product $product)
    {
        abort_unless($product->isOwnedBy(Auth::user()), 403);
        $product->delete();
        return redirect()->route('product.manage')->with('message', 'Product Deleted');
    }
}
