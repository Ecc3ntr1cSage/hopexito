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

    public function discover(Request $request)
    {
        $selectedType = $request->string('type')->toString();
        $allowedTypes = ['shirt', 'sweat', 'hoodie'];
        if (! in_array($selectedType, $allowedTypes, true)) {
            $selectedType = null;
        }

        $productsQuery = Product::available();
        if ($selectedType !== null) {
            $productsQuery->where('product_type', $selectedType);
        }

        $products = $productsQuery->inRandomOrder()->paginate(100)->withQueryString();
        $typeCounts = Product::available()
            ->selectRaw('product_type, COUNT(*) as aggregate')
            ->groupBy('product_type')
            ->pluck('aggregate', 'product_type');

        return view('discover', compact('products', 'selectedType', 'typeCounts'));
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

}
