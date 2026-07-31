<?php

namespace App\Http\Livewire\Manage;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ManageOrder extends Component
{

    public function received($id)
    {
        $order = Order::findOrFail($id);
        abort_unless(Auth::check() ? $order->email === Auth::user()->email : session('last_order_id') === $order->id, 403);
        $order->update(['status' => 4]);

        session()->flash('message','Order Completed');
        return redirect()->back();
    }
    public function render()
    {
        if (Auth::check()) {
            $order = Order::where('email', Auth::user()->email)->orderBy('created_at', 'DESC')->get();
        } else {
            $order = Order::where('id', session('last_order_id'))->get();
            if ($order->isEmpty() && session('delivery_info.email')) {
                $order = Order::where('email', session('delivery_info.email'))
                    ->orderBy('created_at', 'DESC')
                    ->limit(1)
                    ->get();
            }
        }
        return view('livewire.manage.manage-order', compact('order'));
    }
}
