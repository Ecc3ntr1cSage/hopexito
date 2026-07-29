<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class SessionCart
{
    protected string $instance = 'cart';

    public function instance(string $name): static
    {
        $this->instance = $name;
        return $this;
    }

    public function content(): array
    {
        return Session::get($this->instance, []);
    }

    public function add(array $item): array
    {
        $cart = $this->content();
        $rowId = uniqid('cart_');
        $item['rowId'] = $rowId;
        $cart[$rowId] = $item;
        Session::put($this->instance, $cart);
        return $item;
    }

    public function get(string $rowId): ?array
    {
        $cart = $this->content();
        return $cart[$rowId] ?? null;
    }

    public function update(string $rowId, int $quantity): void
    {
        $cart = $this->content();
        if (isset($cart[$rowId])) {
            $cart[$rowId]['qty'] = $quantity;
            Session::put($this->instance, $cart);
        }
    }

    public function remove(string $rowId): void
    {
        $cart = $this->content();
        unset($cart[$rowId]);
        Session::put($this->instance, $cart);
    }

    public function subtotal(): float
    {
        $cart = $this->content();
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['qty'];
        }
        return $total;
    }

    public function count(): int
    {
        return count($this->content());
    }

    public function destroy(): void
    {
        Session::forget($this->instance);
    }
}
