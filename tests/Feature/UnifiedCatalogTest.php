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

    public function test_product_studio_is_the_canonical_creation_route(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('product.create', ['type' => 'sweat']))
            ->assertOk()
            ->assertSee('Product studio')
            ->assertSee('Sweatshirt');

        $this->actingAs($user)->get(route('mockup.hoodie'))
            ->assertRedirect(route('product.create', ['type' => 'hoodie']));
    }

    public function test_a_user_can_create_a_fixed_price_product_with_three_color_variants(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('product.store'), [
            'product_type' => 'hoodie',
            'title' => 'Night Hoodie',
            'tags' => 'night, graphic',
            'visibility' => 'public',
            'rights' => '1',
            'transforms' => [
                'front' => ['x' => 50, 'y' => 50, 'scale' => 1, 'rotation' => 0],
                'back' => ['x' => 48, 'y' => 52, 'scale' => 0.8, 'rotation' => -12],
            ],
            'image_front' => $this->pngUpload('front.png'),
            'image_back' => $this->pngUpload('back.png'),
        ]);

        $response->assertRedirect();
        $product = Product::firstOrFail();
        $response->assertRedirect(route('product.show', $product));
        $this->assertSame('hoodie', $product->product_type);
        $this->assertEquals(70, $product->price);
        $this->assertEquals(0.15, (float) $product->commission_rate);
        $this->assertSame('public', $product->visibility);
        $this->assertCount(3, $product->variants);
        $this->assertSame(['Black', 'Gray', 'White'], $product->variants->pluck('color')->sort()->values()->all());
        $this->assertTrue($product->variants->every(fn ($variant) => filled($variant->image_front_path) && filled($variant->image_back_path)));
        $whiteFront = $product->variants()->where('color', 'White')->value('image_front_path');
        Storage::disk('public')->assertExists($whiteFront);
        $this->assertNotSame(
            file_get_contents(public_path('mockups/white-hoodie-front.png')),
            Storage::disk('public')->get($whiteFront)
        );
    }

    public function test_product_without_back_artwork_uses_plain_back_mockups(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('product.store'), [
            'product_type' => 'shirt',
            'title' => 'Front Only',
            'tags' => 'minimal',
            'visibility' => 'public',
            'preview_side' => 'back',
            'rights' => '1',
            'transforms' => ['front' => ['x' => 50, 'y' => 50, 'scale' => 1, 'rotation' => 0]],
            'image_front' => $this->pngUpload('front.png'),
        ])->assertRedirect();

        $product = Product::firstOrFail();
        $this->assertSame(0, $product->preview);
        $this->assertCount(3, $product->variants);
        $this->assertSame(
            ['Black', 'Gray', 'White'],
            $product->variants->pluck('color')->sort()->values()->all()
        );
        $this->assertTrue($product->variants->every(
            fn ($variant) => $variant->image_back_path === 'mockups/'.strtolower($variant->color).'-shirt-back.png'
        ));
        $this->assertSame('mockups/white-shirt-back.png', $product->variants()->where('color', 'White')->value('image_back_path'));
    }

    public function test_transform_bounds_are_validated_server_side(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)->from(route('product.create'))->post(route('product.store'), [
            'product_type' => 'shirt',
            'title' => 'Invalid transform',
            'tags' => 'test',
            'rights' => '1',
            'transforms' => ['front' => ['x' => 101, 'y' => 50, 'scale' => 1, 'rotation' => 0]],
            'image_front' => $this->pngUpload('front.png'),
        ])->assertRedirect(route('product.create'))
            ->assertSessionHasErrors('transforms.front.x');

        $this->assertDatabaseCount('products', 0);
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

    public function test_product_show_renders_the_purchase_surface(): void
    {
        $owner = User::factory()->create();
        $product = $this->makeProduct($owner, 'public', 'shirt');

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee('product-page')
            ->assertSee('Add to bag')
            ->assertSee('Buy now')
            ->assertSee('Size guide');
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
            'price' => 1,
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

    private function pngUpload(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, file_get_contents(public_path('image/hopexito.png')));
    }
}
