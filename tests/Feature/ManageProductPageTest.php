<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageProductPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_open_the_product_workbench(): void
    {
        $owner = User::factory()->create();

        $this->actingAs($owner)
            ->get(route('product.manage'))
            ->assertOk()
            ->assertSee('Product studio / 02')
            ->assertSee('Your editions')
            ->assertSee('Collections')
            ->assertSee(route('product.create'));
    }
}
