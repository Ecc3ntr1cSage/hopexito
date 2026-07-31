<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UnifiedCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_create_a_fixed_price_product_with_three_color_variants(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('product.store'), [
            'product_type' => 'hoodie',
            'title' => 'Night Hoodie',
            'tags' => 'night, graphic',
            'visibility' => 'public',
            'image_front' => UploadedFile::fake()->create('front.png', 10, 'image/png'),
            'image_back' => UploadedFile::fake()->create('back.png', 10, 'image/png'),
        ]);

        $response->assertRedirect(route('product.manage'));
        $product = Product::firstOrFail();
        $this->assertSame('hoodie', $product->product_type);
        $this->assertEquals(70, $product->price);
        $this->assertEquals(0.15, (float) $product->commission_rate);
        $this->assertSame('public', $product->visibility);
        $this->assertCount(3, $product->variants);
        $this->assertSame(['Black', 'Gray', 'White'], $product->variants->pluck('color')->sort()->values()->all());
    }

    public function test_private_products_are_owner_only(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $product = $this->makeProduct($owner, 'private');

        $this->get(route('product.show', $product))->assertNotFound();
        $this->actingAs($other)->get(route('product.show', $product))->assertNotFound();
        $this->actingAs($owner)->get(route('product.show', $product))->assertOk();
    }

    public function test_owner_purchase_uses_discount_without_creator_earnings(): void
    {
        $owner = User::factory()->create(['phone' => '0123456789', 'address' => '1 Street', 'postcode' => '50000', 'state' => 'Selangor']);
        Wallet::create(['id' => fake()->uuid(), 'user_id' => $owner->id, 'name' => $owner->name, 'commission' => 0, 'balance' => 0, 'status' => 1]);
        $product = $this->makeProduct($owner, 'private', 'shirt');

        $this->actingAs($owner)->post(route('cart.store'), [
            'product_id' => $product->id, 'size' => 'M', 'color' => 'White', 'quantity' => 1, 'buy_now' => true,
        ])->assertRedirect(route('billplz-create'));
        $this->actingAs($owner)->post(route('billplz-store'))->assertRedirect(route('order.index'));

        $line = ProductOrder::firstOrFail();
        $this->assertEquals(29.75, $line->price);
        $this->assertTrue($line->is_owner_purchase);
        $this->assertEquals(1, $product->fresh()->sold);
        $this->assertDatabaseCount('wallet_transactions', 0);
    }

    public function test_external_purchase_pays_fixed_price_and_credits_fifteen_percent(): void
    {
        $owner = User::factory()->create();
        $buyer = User::factory()->create(['phone' => '0123456789', 'address' => '1 Street', 'postcode' => '50000', 'state' => 'Selangor']);
        Wallet::create(['id' => fake()->uuid(), 'user_id' => $owner->id, 'name' => $owner->name, 'commission' => 0, 'balance' => 0, 'status' => 1]);
        $product = $this->makeProduct($owner, 'public', 'shirt');

        $this->actingAs($buyer)->post(route('cart.store'), [
            'product_id' => $product->id, 'size' => 'M', 'color' => 'White', 'quantity' => 1, 'buy_now' => true,
        ])->assertRedirect(route('billplz-create'));
        $this->actingAs($buyer)->post(route('billplz-store'))->assertRedirect(route('order.index'));

        $line = ProductOrder::firstOrFail();
        $this->assertEquals(35, $line->price);
        $this->assertFalse($line->is_owner_purchase);
        $this->assertEquals(1, $product->fresh()->sold);
        $this->assertDatabaseHas('wallet_transactions', ['user_id' => $owner->id, 'income' => 5.25]);
    }

    private function makeProduct(User $owner, string $visibility, string $type = 'hoodie'): Product
    {
        $catalog = config('catalog.types.'.$type);
        $product = Product::create([
            'user_id' => $owner->id,
            'title' => fake()->unique()->sentence(2),
            'slug' => fake()->unique()->slug(),
            'product_type' => $type,
            'visibility' => $visibility,
            'tags' => 'test',
            'price' => $catalog['price'],
            'commission_rate' => 0.15,
            'status' => 1,
            'sold' => 0,
        ]);
        foreach (config('catalog.colors') as $color) {
            ProductVariant::create([
                'product_id' => $product->id,
                'color' => $color,
                'image_front_path' => 'mockups/white-'.$type.'-front.png',
                'image_back_path' => 'mockups/white-'.$type.'-back.png',
            ]);
        }
        return $product;
    }
}
