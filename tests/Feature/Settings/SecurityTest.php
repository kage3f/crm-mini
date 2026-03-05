<?php

namespace Tests\Feature\Settings;

use App\Livewire\Settings\Security;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionsSeeder::class);
    }

    public function test_guest_is_redirected_from_security_page(): void
    {
        $this->get('/settings/security')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_security_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/settings/security')
            ->assertOk();
    }

    public function test_change_password_requires_valid_fields(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Security::class)
            ->set('current_password', '')
            ->set('new_password', 'short')
            ->set('new_password_confirmation', 'short')
            ->call('changePassword')
            ->assertHasErrors(['current_password', 'new_password']);
    }

    public function test_change_password_fails_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('senha-atual'),
        ]);

        $this->actingAs($user);

        Livewire::test(Security::class)
            ->set('current_password', 'senha-errada')
            ->set('new_password', 'NovaSenha123!')
            ->set('new_password_confirmation', 'NovaSenha123!')
            ->call('changePassword')
            ->assertHasErrors(['current_password']);

        $this->assertTrue(Hash::check('senha-atual', $user->fresh()->password));
    }

    public function test_user_can_change_password_with_correct_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('senha-atual'),
        ]);

        $this->actingAs($user);

        Livewire::test(Security::class)
            ->set('current_password', 'senha-atual')
            ->set('new_password', 'NovaSenha123!')
            ->set('new_password_confirmation', 'NovaSenha123!')
            ->call('changePassword')
            ->assertSet('current_password', '')
            ->assertSet('new_password', '')
            ->assertSet('new_password_confirmation', '');

        $this->assertTrue(Hash::check('NovaSenha123!', $user->fresh()->password));
    }
}
