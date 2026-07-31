<?php

namespace App\Http\Livewire\Manage;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ManageSales extends Component
{
    // return total commission
    private function totalCommission()
    {
        $totalCommission = 0;
        $commission = 0;
        $products = Product::where('user_id', Auth::id())->get();
        foreach ($products as $product) {
            $commission = (float) $product->price * (float) $product->commission_rate;
            foreach ($product->productOrder->where('is_owner_purchase', false) as $item) {
                $totalCommission += $commission * $item->quantity;
            }
        }
        return $totalCommission;
    }

    public function render()
    {
        $products = Product::with('productOrder')
            ->where('user_id', Auth::id())
            ->get();
        $productOrders = ProductOrder::with('order')
            ->whereIn('product_id', $products->pluck('id'))
            ->where('is_owner_purchase', false)
            ->get();
        $totalItem = Product::where('user_id', Auth::id())->sum('sold');
        $totalCommission = $this->totalCommission();

        return view('livewire.manage.manage-sales', compact('productOrders','totalItem', 'totalCommission'));
    }
}
