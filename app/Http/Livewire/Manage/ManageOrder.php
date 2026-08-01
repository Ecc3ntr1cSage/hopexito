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
            $orders = Order::with('productOrder.product')
                ->where('email', Auth::user()->email)
                ->orderByDesc('created_at')
                ->get();
        } else {
            $orders = Order::with('productOrder.product')
                ->where('id', session('last_order_id'))
                ->get();
            if ($orders->isEmpty() && session('delivery_info.email')) {
                $orders = Order::with('productOrder.product')
                    ->where('email', session('delivery_info.email'))
                    ->orderBy('created_at', 'DESC')
                    ->limit(1)
                    ->get();
            }
        }

        $stats = [
            'orders' => $orders->count(),
            'items' => $orders->sum(fn ($order) => $order->productOrder->sum('quantity')),
            'active' => $orders->where('status', '!=', 4)->count(),
            'spent' => $orders->sum('amount'),
        ];

        return view('livewire.manage.manage-order', compact('orders', 'stats'));
    }
}
