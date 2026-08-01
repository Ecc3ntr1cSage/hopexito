<?php

namespace App\Http\Livewire\Manage;

use App\Models\Order;
use App\Models\ProductOrder;
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
        $sales = collect();

        if (Auth::check()) {
            $orders = Order::with('productOrder.product')
                ->where('email', Auth::user()->email)
                ->orderByDesc('created_at')
                ->get();

            $sales = ProductOrder::with(['order', 'product.variants'])
                ->where('is_owner_purchase', false)
                ->whereHas('product', fn ($query) => $query->where('user_id', Auth::id()))
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

        $salesStats = [
            'orders' => $sales->pluck('billplz_id')->unique()->count(),
            'items' => $sales->sum('quantity'),
            'gross' => $sales->sum(fn ($item) => (float) $item->price * (int) $item->quantity),
            'earnings' => $sales->sum(fn ($item) => (float) $item->price * (int) $item->quantity * (float) ($item->product?->commission_rate ?? 0.15)),
        ];

        return view('livewire.manage.manage-order', compact('orders', 'stats', 'sales', 'salesStats'));
    }
}
