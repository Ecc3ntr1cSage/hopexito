<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_delivery_and_payment_surfaces_render_as_one_flow(): void
    {
        $user = User::factory()->create([
            'phone' => '0123456789', 'address' => '1 Hope Street', 'postcode' => '50000', 'state' => 'Selangor',
        ]);
        $product = $this->makeProduct($user);

        $this->actingAs($user)->post(route('cart.store'), [
            'product_id' => $product->id, 'size' => 'M', 'color' => 'White', 'quantity' => 1,
        ])->assertRedirect(route('cart.index'));

        $this->actingAs($user)->get(route('cart.index'))
            ->assertOk()
            ->assertSee('cart-page')
            ->assertSee('Continue to delivery');
        $this->actingAs($user)->get(route('guest.checkout'))
            ->assertOk()
            ->assertSee('checkout-page')
            ->assertSee('Continue to payment');
        $this->actingAs($user)->get(route('billplz-create'))
            ->assertOk()
            ->assertSee('payment-page')
            ->assertSee('Simulate success');
    }

    public function test_failed_payment_returns_to_delivery_without_creating_an_order(): void
    {
        $user = User::factory()->create([
            'phone' => '0123456789', 'address' => '1 Hope Street', 'postcode' => '50000', 'state' => 'Selangor',
        ]);
        $product = $this->makeProduct($user);

        $this->actingAs($user)->post(route('cart.store'), [
            'product_id' => $product->id, 'size' => 'M', 'color' => 'White', 'quantity' => 1,
        ]);

        $this->actingAs($user)->post(route('billplz-store'), ['payment_result' => 'failed'])
            ->assertRedirect(route('guest.checkout'))
            ->assertSessionHas('message');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_guest_success_redirects_to_the_order_receipt(): void
    {
        $owner = User::factory()->create();
        $product = $this->makeProduct($owner);

        $this->withSession([
            'delivery_info' => [
                'name' => 'Guest Buyer', 'email' => 'guest@example.com', 'phone' => '0123456789',
                'address' => '2 Hope Street', 'postcode' => '50100', 'state' => 'Kuala Lumpur',
            ],
        ])->post(route('cart.store'), [
            'product_id' => $product->id, 'size' => 'L', 'color' => 'White', 'quantity' => 1,
        ])->assertRedirect(route('cart.index'));

        $this->followingRedirects()->post(route('billplz-store'), ['payment_result' => 'success'])
            ->assertOk()
            ->assertSee('2 Hope Street')
            ->assertSee('Your Orders');
        $this->assertDatabaseCount('orders', 1);
    }

    private function makeProduct(User $owner): Product
    {
        $product = Product::create([
            'user_id' => $owner->id,
            'title' => 'Field Notes Tee',
            'slug' => 'field-notes-tee-'.uniqid(),
            'product_type' => 'shirt',
            'visibility' => 'public',
            'tags' => 'test',
            'price' => 35,
            'commission_rate' => .15,
            'status' => 1,
            'sold' => 0,
        ]);
        ProductVariant::create([
            'product_id' => $product->id,
            'color' => 'White',
            'image_front_path' => 'mockups/white-shirt-front.png',
            'image_back_path' => 'mockups/white-shirt-back.png',
        ]);

        return $product;
    }
}
