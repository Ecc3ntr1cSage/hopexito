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
    public function create(Request $request)
    {
        $catalog = config('catalog.types');
        $initialType = $request->query('type', 'shirt');

        abort_unless(array_key_exists($initialType, $catalog), 404);

        return view('mockup.editor', [
            'catalog' => $catalog,
            'colors' => config('catalog.colors'),
            'canvas' => config('catalog.canvas'),
            'initialType' => $initialType,
            'assetBase' => asset('mockups'),
        ]);
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
            'preview_side' => ['nullable', 'in:front,back'],
            'rights' => ['accepted'],
            'transforms' => ['required', 'array'],
            'transforms.front' => ['required', 'array'],
            'transforms.front.x' => ['required', 'numeric', 'between:0,100'],
            'transforms.front.y' => ['required', 'numeric', 'between:0,100'],
            'transforms.front.scale' => ['required', 'numeric', 'between:0.25,2'],
            'transforms.front.rotation' => ['required', 'numeric', 'between:-180,180'],
            'transforms.back' => ['nullable', 'array'],
            'transforms.back.x' => ['nullable', 'numeric', 'between:0,100'],
            'transforms.back.y' => ['nullable', 'numeric', 'between:0,100'],
            'transforms.back.scale' => ['nullable', 'numeric', 'between:0.25,2'],
            'transforms.back.rotation' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $catalog = config('catalog.types.'.$validated['product_type']);
        abort_unless($catalog, 422, 'Unknown product type.');

        $hasBackDesign = $request->hasFile('image_back');
        if ($hasBackDesign) {
            $request->validate([
                'transforms.back.x' => ['required', 'numeric', 'between:0,100'],
                'transforms.back.y' => ['required', 'numeric', 'between:0,100'],
                'transforms.back.scale' => ['required', 'numeric', 'between:0.25,2'],
                'transforms.back.rotation' => ['required', 'numeric', 'between:-180,180'],
            ]);
        }
        $previewSide = $hasBackDesign && ($validated['preview_side'] ?? 'front') === 'back' ? 1 : 0;

        $product = DB::transaction(function () use ($request, $validated, $catalog, $mockups, $hasBackDesign, $previewSide) {
            $product = Product::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'slug' => Str::random(30),
                'product_type' => $validated['product_type'],
                'visibility' => $validated['visibility'] ?? 'public',
                'tags' => $validated['tags'],
                'price' => $catalog['price'],
                'commission_rate' => config('catalog.commission_rate'),
                'collection_id' => null,
                'status' => 1,
                'sold' => 0,
                'preview' => $previewSide,
            ]);

            foreach (config('catalog.colors') as $color) {
                $frontPath = $mockups->generate(
                    $request->file('image_front'),
                    'mockups/'.strtolower($color).'-'.$validated['product_type'].'-front.png',
                    $catalog['front_position'],
                    $color,
                    'products/'.$product->id.'/'.strtolower($color).'-front.png',
                    $validated['transforms']['front']
                );

                if ($hasBackDesign) {
                    $backPath = $mockups->generate(
                        $request->file('image_back'),
                        'mockups/'.strtolower($color).'-'.$validated['product_type'].'-back.png',
                        $catalog['back_position'],
                        $color,
                        'products/'.$product->id.'/'.strtolower($color).'-back.png',
                        $validated['transforms']['back'] ?? []
                    );
                } else {
                    $backPath = 'mockups/'.strtolower($color).'-'.$validated['product_type'].'-back.png';
                }

                ProductVariant::create([
                    'product_id' => $product->id,
                    'color' => $color,
                    'image_front_path' => $frontPath,
                    'image_back_path' => $backPath,
                ]);
            }

            return $product;
        });

        session()->flash('message', 'Product Created');
        return redirect()->route('product.show', $product);
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
