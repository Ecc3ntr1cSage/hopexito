<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatorProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_profile_exposes_workspace_destinations(): void
    {
        $owner = User::factory()->create();
        $product = Product::create([
            'user_id' => $owner->id,
            'title' => 'Archive piece',
            'slug' => 'archive-piece',
            'product_type' => 'shirt',
            'visibility' => 'public',
            'tags' => 'archive',
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
            ->get(route('people', $owner->name))
            ->assertOk()
            ->assertSee('Your workspace')
            ->assertSee(route('profile.show'))
            ->assertSee(route('product.manage'))
            ->assertSee('Manage products')
            ->assertDontSee('profile-cover')
            ->assertDontSee('cover_image');
    }
}
