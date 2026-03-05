<?php

namespace Tests\Feature\Settings;

use App\Livewire\Settings\Company;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionsSeeder::class);
    }

    public function test_guest_is_redirected_from_company_page(): void
    {
        $this->get('/settings/company')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_company_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/settings/company')
            ->assertOk();
    }

    public function test_user_can_update_company_name(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Company::class)
            ->set('company_name', 'Nova Empresa')
            ->call('save');

        $this->assertDatabaseHas('companies', [
            'id' => $user->company_id,
            'name' => 'Nova Empresa',
        ]);
    }

    public function test_company_requires_valid_name(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Company::class)
            ->set('company_name', '')
            ->call('save')
            ->assertHasErrors(['company_name']);
    }
}
