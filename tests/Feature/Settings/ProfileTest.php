<?php

namespace Tests\Feature\Settings;

use App\Livewire\Settings\Profile;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionsSeeder::class);
    }

    public function test_guest_is_redirected_from_profile_page(): void
    {
        $this->get('/settings/profile')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_profile_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/settings/profile')
            ->assertOk();
    }

    public function test_user_can_update_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Nome Antigo',
            'email' => 'antigo@exemplo.com',
        ]);

        $this->actingAs($user);

        Livewire::test(Profile::class)
            ->set('name', 'Nome Novo')
            ->set('email', 'novo@exemplo.com')
            ->call('save');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nome Novo',
            'email' => 'novo@exemplo.com',
        ]);
    }

    public function test_profile_requires_valid_fields(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Profile::class)
            ->set('name', '')
            ->set('email', 'email-invalido')
            ->call('save')
            ->assertHasErrors(['name', 'email']);
    }

    public function test_profile_email_must_be_unique(): void
    {
        $existing = User::factory()->create([
            'email' => 'existe@exemplo.com',
        ]);
        $user = User::factory()->create([
            'email' => 'meu@exemplo.com',
        ]);

        $this->actingAs($user);

        Livewire::test(Profile::class)
            ->set('name', $user->name)
            ->set('email', $existing->email)
            ->call('save')
            ->assertHasErrors(['email']);
    }

    public function test_user_can_upload_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Profile::class)
            ->set('name', $user->name)
            ->set('email', $user->email)
            ->set('avatar', UploadedFile::fake()->image('avatar.jpg'))
            ->call('save')
            ->assertHasNoErrors();

        $user->refresh();

        $this->assertNotNull($user->avatar_url);
        Storage::disk('public')->assertExists($user->avatar_url);
    }
}
