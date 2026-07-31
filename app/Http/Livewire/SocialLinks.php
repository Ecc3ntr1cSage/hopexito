<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Profile;
use App\Models\TemporaryFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UploadController;
use Livewire\WithFileUploads;

class SocialLinks extends Component
{
    use WithFileUploads;

    public $facebook, $twitter, $instagram, $dribble, $behance, $pinterest, $deviantart, $tiktok, $filename, $cover_image, $website;

    public function store()
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
            'website' => $this->website
        ]);
        
        session()->flash('message', 'Links Updated');
        return redirect()->route('people', Auth::user()->name);  
    }

    private function forceFill()
    {
        if (Profile::where('user_id', Auth::id())->exists()) {
            $profile = Profile::where('user_id', Auth::id())->firstOrFail();
            $this->facebook = $profile->facebook;
            $this->twitter = $profile->twitter;
            $this->instagram = $profile->instagram;
            $this->dribble = $profile->dribble;
            $this->behance = $profile->behance;
            $this->pinterest = $profile->pinterest;
            $this->deviantart = $profile->deviantart;
            $this->tiktok = $profile->tiktok;
            $this->website = $profile->website;
        }
    }

    public function render()
    {
        $this->forceFill();
        return view('livewire.social-links');
    }
}
