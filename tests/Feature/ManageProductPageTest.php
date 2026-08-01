<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManageProductPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_open_the_product_workbench(): void
    {
        $owner = User::factory()->create();
        $product = Product::create([
            'user_id' => $owner->id,
            'title' => 'Workbench Tee',
            'slug' => 'workbench-tee',
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
            'image_front_path' => 'mockups/shirt/white-shirt-front.png',
            'image_back_path' => 'mockups/shirt/white-shirt-back.png',
        ]);

        $this->actingAs($owner)
            ->get(route('product.manage'))
            ->assertOk()
            ->assertSee('Product studio / 02')
            ->assertSee('Your editions')
            ->assertSee('Archives')
            ->assertDontSee('Collections')
            ->assertSee('View product')
            ->assertSee('Pin to profile')
            ->assertSee('Edit details')
            ->assertSee('Archive')
            ->assertSee(route('product.create'))
            ->assertSee(route('logout'))
            ->assertSee('Log out');
    }

    public function test_deleting_a_product_removes_generated_media_but_keeps_shared_mockups(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $product = Product::create([
            'user_id' => $owner->id,
            'title' => 'Disposable Tee',
            'slug' => 'disposable-tee',
            'product_type' => 'shirt',
            'visibility' => 'private',
            'tags' => 'test',
            'price' => 35,
            'commission_rate' => .15,
            'status' => 1,
            'sold' => 0,
        ]);
        ProductVariant::create([
            'product_id' => $product->id,
            'color' => 'White',
            'image_front_path' => 'products/'.$product->id.'/white-front.png',
            'image_back_path' => 'products/'.$product->id.'/white-back.png',
        ]);
        Storage::disk('public')->put('products/'.$product->id.'/white-front.png', 'front');
        Storage::disk('public')->put('products/'.$product->id.'/white-back.png', 'back');
        Storage::disk('public')->put('mockups/shirt/white-shirt-front.png', 'shared');

        $product->delete();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing('products/'.$product->id.'/white-front.png');
        Storage::disk('public')->assertMissing('products/'.$product->id.'/white-back.png');
        Storage::disk('public')->assertExists('mockups/shirt/white-shirt-front.png');
    }
}
