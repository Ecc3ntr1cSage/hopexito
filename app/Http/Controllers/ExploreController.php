<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Artist;
use App\Models\ProductCollection;
use App\Models\Search;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ExploreController extends Controller
{

    // views/sellyourart
    public function sellyourart()
    {
        $sellers = User::where('role_id', 2)
            ->withCount('products')
            ->has('products', '>', 0)
            ->inRandomOrder()
            ->take(5)
            ->get();

        return view('sellyourart', compact('sellers'));
    }
    // views/explore
    public function explore()
    {
        $users = User::where('role_id', 2)
            ->withCount('products')
            ->has('products', '>', 0)
            ->inRandomOrder()
            ->take(5)
            ->get();

        $featured = $users->pluck('id');
        $products = Product::where('status', 1)
            ->whereIn('artist_id', $featured)
            ->inRandomOrder()
            ->take(8)
            ->get();

        if ($products->isEmpty()) {
            $products = Product::where('status', 1)->inRandomOrder()->take(8)->get();
        }

        $collections = ProductCollection::inRandomOrder()
            ->whereHas('product', function ($query) {
                $query->select('collection_id')
                    ->groupBy('collection_id')
                    ->havingRaw('COUNT(*) > 1');
            })
            ->take(2)
            ->get();
        return view('explore', compact('users', 'products', 'collections'));
    }
    // return search results and track search history
    public function search(Request $request)
    {
        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
        } else {
            return redirect()->route('shop.all');
        }

        $artists = User::where('role_id', 2)
            ->where('name', 'LIKE', "%{$search}%")
            ->get();

        $products = Product::where('status', '!=', 2)
            ->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('shopname', 'LIKE', "%{$search}%")
                    ->orWhere('category', 'LIKE', "%{$search}%")
                    ->orWhere('tags', 'LIKE', "%{$search}%");
            })
            ->paginate(40);

        if (Auth::check()) {
            Search::create([
                'user_id' => Auth::user()->id,
                'keyword' => $search
            ]);
        }
        $product_count = $products->total();
        $user_count = $artists->count();

        return view('shop/search', compact('artists', 'products', 'search', 'product_count', 'user_count'));
    }

    public function collection()
    {
        $productsCollection = ProductCollection::with('product:slug,collection_id,product_image,product_image_2,title,price,shopname')
            ->whereHas('product', function ($query) {
                $query->select('collection_id')
                    ->groupBy('collection_id')
                    ->havingRaw('COUNT(*) > 1');
            })
            ->inRandomOrder()
            ->paginate(5);
        return view('shop/collection', compact('productsCollection'));
    }

    public function shop()
    {
        $products = Product::where('status', '!=', 2)->inrandomOrder()->paginate(100);
        return view('shop/all', compact('products'));
    }

    public function shirt()
    {
        $products = Product::where('status', '!=', 2)->where('category', 'shirt')->inrandomOrder()->paginate(100);
        return view('shop/standard-tee', compact('products'));
    }

    public function oversized()
    {
        $products = Product::where('status', '!=', 2)->where('category', 'oversized')->inrandomOrder()->paginate(100);
        return view('shop/oversized', compact('products'));
    }
    // return seller profile, views/people
    public function people($shopname)
    {
        $user = User::where('name', $shopname)->first();
        $productsQuery = Product::where('artist_id', $user->id)->where('status', '!=', 2);
        $productsCount = $productsQuery->count();
        $products = $productsQuery->orderBy('status', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(16);
        $productsCollection = ProductCollection::where('name', $shopname)->get();
        $totalSold = Product::where('artist_id', $user->id)->sum('sold');

        return view('people', compact('user', 'products', 'productsCount', 'productsCollection', 'totalSold'));
    }
    // upgrade user from customer to seller
    public function upgrade($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'role_id' => 2
        ]);
        Artist::create([
            'id' => Auth::user()->id
        ]);
        Wallet::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::user()->id,
            'name' => Auth::user()->name,
            'commission' => 0,
            'balance' => 0,
            'status' => 1
        ]);

        $user->assignRole('artist');
        session()->flash('message', 'You have been upgraded');
        return redirect()->route('dashboard');
    }
}
