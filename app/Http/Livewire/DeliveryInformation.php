<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DeliveryInformation extends Component
{
    public $name, $email, $phone, $address, $postcode, $state;

    public function mount(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->fill([
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'postcode' => $user->postcode,
                'state' => $user->state,
            ]);
            return;
        }

        if ($details = session('delivery_info')) {
            $this->fill($details);
        }
    }

    public function storeDeliveryInfo()
    {
        $this->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'postcode' => 'required|string',
            'state' => 'required|string',
        ]);

        session([
            'delivery_info' => [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'postcode' => $this->postcode,
                'state' => $this->state,
            ]
        ]);

        return redirect()->route('billplz-create');
    }
    
    public function render()
    {
        return view('livewire.delivery-information');
    }
}
