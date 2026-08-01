<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Search;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StorefrontController extends Controller
{
    public function home()
    {
        $users = User::whereHas('products', fn ($query) => $query->available())
            ->withCount(['products' => fn ($query) => $query->available()])
            ->inRandomOrder()
            ->take(5)
            ->get();

        $products = Product::available()->inRandomOrder()->take(8)->get();
        return view('home', compact('users', 'products'));
    }

    public function search(Request $request)
    {
        $search = trim((string) $request->input('search'));
        if ($search === '') {
            return redirect()->route('discover');
        }

        $users = User::where('name', 'LIKE', "%{$search}%")
            ->whereHas('products', fn ($query) => $query->available())
            ->get();
        $products = Product::available()
            ->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('tags', 'LIKE', "%{$search}%")
                    ->orWhere('product_type', 'LIKE', "%{$search}%")
                    ->orWhereHas('owner', fn ($owner) => $owner->where('name', 'LIKE', "%{$search}%"));
            })
            ->paginate(40)
            ->withQueryString();

        if (Auth::check()) {
            Search::create(['user_id' => Auth::id(), 'keyword' => $search]);
        }

        $product_count = $products->total();
        $user_count = $users->count();
        return view('shop/search', compact('users', 'products', 'search', 'product_count', 'user_count'));
    }

    public function discover()
    {
        $products = Product::available()->inRandomOrder()->paginate(100);
        return view('discover', compact('products'));
    }

    public function shirt()
    {
        return $this->type('shirt', 'shop/standard-tee');
    }

    public function sweat()
    {
        return $this->type('sweat', 'shop/oversized');
    }

    public function hoodie()
    {
        return $this->type('hoodie', 'shop/oversized');
    }

    public function people(string $name)
    {
        $user = User::where('name', $name)->firstOrFail();
        $isOwner = Auth::check() && Auth::id() === $user->id;
        $productsQuery = Product::where('user_id', $user->id)->where('status', '!=', 2);
        if (! $isOwner) {
            $productsQuery->public();
        }

        $productsCount = (clone $productsQuery)->count();
        $products = $productsQuery->orderByDesc('status')->orderByDesc('created_at')->paginate(16);
        $totalSold = Product::where('user_id', $user->id)->sum('sold');

        return view('people', compact('user', 'products', 'productsCount', 'totalSold'));
    }

    private function type(string $type, string $view)
    {
        $products = Product::available()->where('product_type', $type)->inRandomOrder()->paginate(100);
        $productType = config('catalog.types.'.$type.'.label');
        return view($view, compact('products', 'productType'));
    }
}
