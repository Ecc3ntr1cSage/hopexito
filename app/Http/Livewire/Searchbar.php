<?php

namespace App\Http\Livewire;

use App\Models\Search;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Searchbar extends Component
{
    public function deleteSearch($id)
    {
        $search = Search::find($id);
        if ($search) {
            $search->delete();
        }
    }

    public function render()
    {
        $trending_searches = Search::select('keyword', DB::raw('COUNT(*) as count'))
            ->groupBy('keyword')
            ->orderBy('count', 'desc')
            ->limit(6)
            ->pluck('keyword');
        if (Auth::check()) {
            $keywords = Search::where('user_id', Auth::user()->id)
                ->orderByDesc('created_at')
                ->limit(4)
                ->pluck('keyword', 'id');
        } else {
            $keywords = "Trending Search";
        }
        $top_users = Wallet::orderBy('commission', 'desc')
            ->pluck('user_id')
            ->take(4);
        $users = User::whereIn('id', $top_users)
            ->with('wallet')
            ->get(['name', 'profile_photo_path']);

        return view('livewire.searchbar', compact('keywords', 'trending_searches','users'));
    }
}
