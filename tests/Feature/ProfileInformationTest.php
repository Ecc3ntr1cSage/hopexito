<?php

namespace Tests\Feature;

use App\Http\Livewire\ProfileBio;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileInformationTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_settings_page_renders_the_new_workspace(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee('Shape the way')
            ->assertSee('Save identity')
            ->assertSee('Save bio')
            ->assertDontSee('Cover image')
            ->assertSee('See the work move.');
    }

    public function test_profile_presence_only_exposes_bio(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertDontSee('cover_image')
            ->assertDontSee('settings-cover');

        $this->assertTrue(Schema::hasTable('profiles'));
        $this->assertFalse(Schema::hasColumn('profiles', 'cover_image'));

        Livewire::test(ProfileBio::class)
            ->set('bio', 'A small studio making useful things.')
            ->call('updateBio');

        $this->assertSame('A small studio making useful things.', Profile::where('user_id', $user->id)->value('bio'));
    }

    public function test_current_profile_information_is_available()
    {
        $this->actingAs($user = User::factory()->create());

        $component = Livewire::test(UpdateProfileInformationForm::class);

        $this->assertEquals($user->name, $component->state['name']);
        $this->assertEquals($user->email, $component->state['email']);
    }

    public function test_profile_information_can_be_updated()
    {
        $this->actingAs($user = User::factory()->create());

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['name' => 'Test Name', 'email' => 'test@example.com'])
            ->call('updateProfileInformation');

        $this->assertEquals('Test Name', $user->fresh()->name);
        $this->assertEquals('test@example.com', $user->fresh()->email);
    }
}
