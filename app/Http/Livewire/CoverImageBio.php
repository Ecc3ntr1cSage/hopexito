<?php

namespace App\Http\Livewire;

use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CoverImageBio extends Component
{
    public $cover_image, $bio;

    public function updateBio(){
        $profile = Profile::firstOrCreate(['user_id' => Auth::id()]);
        $profile->update([
            'bio' => $this->bio
        ]);

        session()->flash('message','Bio Updated');
        return redirect()->route('profile.show');
    }

    private function forceFill()
    {
        if (Profile::where('user_id', Auth::id())->exists()) {
            $profile = Profile::where('user_id', Auth::id())->firstOrFail();
            if($profile->cover_image){
                $this->cover_image = $profile->cover_image;
            }
            if($profile->bio){
                $this->bio = $profile->bio;
            }
        }
    }
    public function render()
    {
        $this->forceFill();
        return view('livewire.cover-image-bio');
    }
}
