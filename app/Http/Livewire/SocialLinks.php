<?php

namespace App\Http\Livewire;

use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SocialLinks extends Component
{
    public $facebook;

    public $twitter;

    public $instagram;

    public $dribble;

    public $behance;

    public $pinterest;

    public $deviantart;

    public $tiktok;

    public $website;

    public function mount(): void
    {
        $profile = Auth::user()->profile;

        if ($profile) {
            $this->fill($profile->only([
                'facebook',
                'twitter',
                'instagram',
                'dribble',
                'behance',
                'pinterest',
                'deviantart',
                'tiktok',
                'website',
            ]));
        }
    }

    public function store(): void
    {
        Profile::updateOrCreate(['user_id' => Auth::id()], [
            'facebook' => $this->facebook,
            'twitter' => $this->twitter,
            'instagram' => $this->instagram,
            'dribble' => $this->dribble,
            'behance' => $this->behance,
            'pinterest' => $this->pinterest,
            'deviantart' => $this->deviantart,
            'tiktok' => $this->tiktok,
            'website' => $this->website,
        ]);

        session()->flash('message', 'Links Updated');

        $this->redirectRoute('people', ['shopname' => Auth::user()->name]);
    }

    public function render()
    {
        return view('livewire.social-links');
    }
}
