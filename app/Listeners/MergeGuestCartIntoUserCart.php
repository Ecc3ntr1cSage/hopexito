<?php

namespace App\Listeners;

use App\Facades\SessionCart;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Auth\Events\Login;

class MergeGuestCartIntoUserCart
{
    public function handle(Login $event): void
    {
        $guestCart = SessionCart::instance('cart')->content();

        if ($guestCart === []) {
            return;
        }

        foreach ($guestCart as $item) {
            $product = Product::find($item['id'] ?? null);

            if (! $product || $product->status === 2 || ! $product->canBeViewedBy($event->user)) {
                continue;
            }

            $quantity = (int) ($item['qty'] ?? 0);
            if ($quantity < 1) {
                continue;
            }

            $ownerPurchase = $product->isOwnedBy($event->user);
            $unitPrice = round((float) $product->price * ($ownerPurchase ? 0.85 : 1), 2);

            Cart::create([
                'id' => uniqid(),
                'product_id' => $product->id,
                'email' => $event->user->email,
                'title' => $product->title,
                'quantity' => $quantity,
                'price' => $unitPrice,
                'subtotal' => $unitPrice * $quantity,
                'weight' => 500 * $quantity,
                'size' => $item['options']['size'] ?? 'M',
                'color' => $item['options']['color'] ?? '',
            ]);
        }

        SessionCart::instance('cart')->destroy();
    }
}
