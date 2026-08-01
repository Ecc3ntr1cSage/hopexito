<?php

namespace App\Http\Livewire;

use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfileBio extends Component
{
    public string $bio = '';

    public function mount(): void
    {
        $this->bio = (string) Auth::user()->profile?->bio;
    }

    public function updateBio(): void
    {
        $this->validate([
            'bio' => ['nullable', 'string', 'max:750'],
        ]);

        Profile::updateOrCreate(
            ['user_id' => Auth::id()],
            ['bio' => filled($this->bio) ? trim($this->bio) : null],
        );

        $this->dispatch('saved');
        session()->flash('message', 'Bio updated.');
    }

    public function render()
    {
        return view('livewire.profile-bio');
    }
}
