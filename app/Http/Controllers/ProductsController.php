<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Artist;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\TemporaryFile;
use App\Models\ProductTemplate;
use App\Services\MockupGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductsController extends Controller
{
    public function index()
    {
        $template = ProductTemplate::all();
        return view('product.custom', compact('template'));
    }
    // product template selection page
    public function create()
    {
        $template = ProductTemplate::all();
        return view('product.create', compact('template'));
    }
    
    // store products into database
    public function store(Request $request, MockupGenerator $mockups)
    {
        $minimum_price = $request->input('min');
        $validated = $request->validate([
            'title' => 'required|max:255',
            'tags' => 'required|max:255',
            'price' => 'required|numeric|min:' . $minimum_price,
            'commission' => 'required|numeric',
            'color' => 'required|array',
            'preview_color' => 'required|string',
            'category' => 'required|string',
            'image_front' => 'required|image|max:8192',
            'image_back' => 'nullable|image|max:8192',
            'template_front' => 'required|string',
            'template_back' => 'nullable|string',
            'front_x' => 'required|integer',
            'front_y' => 'required|integer',
            'front_w' => 'required|integer',
            'front_h' => 'required|integer',
            'back_x' => 'nullable|integer',
            'back_y' => 'nullable|integer',
            'back_w' => 'nullable|integer',
            'back_h' => 'nullable|integer',
        ]);

        $frontDesignPath = $request->file('image_front')->store('image-front', 'public');
        $backDesignPath = $request->file('image_back')?->store('image-back', 'public');

        $frontMockupPath = $mockups->generate(
            $request->file('image_front'),
            $validated['template_front'],
            ['x' => $validated['front_x'], 'y' => $validated['front_y'], 'w' => $validated['front_w'], 'h' => $validated['front_h']],
            $validated['preview_color']
        );

        $backMockupPath = null;
        if ($request->hasFile('image_back') && $request->filled('template_back')) {
            $backMockupPath = $mockups->generate(
                $request->file('image_back'),
                $validated['template_back'],
                [
                    'x' => $validated['back_x'] ?? $validated['front_x'],
                    'y' => $validated['back_y'] ?? $validated['front_y'],
                    'w' => $validated['back_w'] ?? $validated['front_w'],
                    'h' => $validated['back_h'] ?? $validated['front_h'],
                ],
                $validated['preview_color']
            );
        }

        Product::create([
            'title' => $validated['title'],
            'slug' => Str::random(30),
            'tags' => $validated['tags'],
            'artist_id' => Auth::id(),
            'shopname' => Auth::user()->name,
            'collection_id' => $request->input('collection_id'),
            'price' => $validated['price'],
            'discount' => $request->input('discount', 1),
            'commission' => $validated['commission'],
            'color' => implode(',', $validated['color']),
            'category' => $validated['category'],
            'image_front' => basename($frontDesignPath),
            'image_front_path' => $frontMockupPath,
            'image_back' => $backDesignPath ? basename($backDesignPath) : null,
            'image_back_path' => $backMockupPath,
            'product_image_path' => $frontMockupPath,
            'product_image_2_path' => $backMockupPath,
            'preview' => intval($request->input('preview', 0)),
        ]);

        session()->flash('message', 'Product Created');
        return redirect()->route('product.create');
    }
    // display product page, views/product/show
    public function show(Product $product)
    {
        $user = User::where('name', $product->shopname)->first();
        $products = Product::where('shopname', $product->shopname)->inRandomOrder()->take(8)->get();
        $discover = Product::where('shopname', '!=', $product->shopname)->inRandomOrder()->take(8)->get();
        $totalDesigns = Product::where('shopname', $product->shopname)->count();
        $colors = explode(',', $product->color);
        return view('product.show', compact('product', 'products', 'user', 'colors', 'totalDesigns', 'discover'));
    }

    public function edit(Product $product)
    {
        //
    }

    public function update(Request $request, Product $product)
    {
        //
    }

    public function destroy(Product $product)
    {
        //
    }
}
