<?php

namespace Tests\Feature\Clients;

use App\Livewire\Clients\Index as ClientsIndex;
use App\Models\Client;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class ClientsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionsSeeder::class);
    }

    private function createUserWithPermissions(array $permissions = []): User
    {
        $user = User::factory()->create();
        $user->assignRole('member');

        if (!empty($permissions)) {
            $user->givePermissionTo($permissions);
        }

        return $user;
    }

    public function test_guest_is_redirected_from_clients_page(): void
    {
        $this->get('/clients')->assertRedirect('/login');
    }

    public function test_user_without_view_permission_cannot_access_clients_page(): void
    {
        $user = $this->createUserWithPermissions([]);

        $this->actingAs($user)
            ->get('/clients')
            ->assertForbidden();
    }

    public function test_user_with_view_permission_can_access_clients_page(): void
    {
        $user = $this->createUserWithPermissions(['clients.view']);

        $this->actingAs($user)
            ->get('/clients')
            ->assertOk();
    }

    public function test_admin_can_access_clients_without_explicit_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get('/clients')
            ->assertOk();
    }

    public function test_user_with_create_permission_can_create_client(): void
    {
        $user = $this->createUserWithPermissions(['clients.view', 'clients.create']);

        $this->actingAs($user);

        Livewire::test(ClientsIndex::class)
            ->set('name', 'Cliente Novo')
            ->set('email', 'cliente@exemplo.com')
            ->set('phone', '11999999999')
            ->set('company', 'Empresa XPTO')
            ->set('status', 'lead')
            ->set('notes', 'Primeiro contato')
            ->call('save');

        $this->assertDatabaseHas('clients', [
            'name' => 'Cliente Novo',
            'email' => 'cliente@exemplo.com',
            'company' => 'Empresa XPTO',
            'status' => 'lead',
            'company_id' => $user->company_id,
            'created_by' => $user->id,
        ]);
    }

    public function test_create_requires_valid_fields(): void
    {
        $user = $this->createUserWithPermissions(['clients.view', 'clients.create']);

        $this->actingAs($user);

        Livewire::test(ClientsIndex::class)
            ->set('name', '')
            ->set('email', 'email-invalido')
            ->set('status', 'invalid')
            ->set('notes', Str::repeat('a', 6000))
            ->call('save')
            ->assertHasErrors(['name', 'email', 'status', 'notes']);
    }

    public function test_user_without_create_permission_cannot_open_create_modal(): void
    {
        $user = $this->createUserWithPermissions(['clients.view']);

        $this->actingAs($user);

        Livewire::test(ClientsIndex::class)
            ->call('openCreateModal')
            ->assertStatus(403);
    }

    public function test_user_without_update_permission_cannot_edit_client(): void
    {
        $user = $this->createUserWithPermissions(['clients.view']);
        $client = Client::factory()->create([
            'company_id' => $user->company_id,
        ]);

        $this->actingAs($user);

        Livewire::test(ClientsIndex::class)
            ->call('openEditModal', $client->id)
            ->assertStatus(403);
    }

    public function test_user_with_update_permission_can_edit_client(): void
    {
        $user = $this->createUserWithPermissions(['clients.view', 'clients.update']);
        $client = Client::factory()->create([
            'company_id' => $user->company_id,
        ]);

        $this->actingAs($user);

        Livewire::test(ClientsIndex::class)
            ->call('openEditModal', $client->id)
            ->set('name', 'Cliente Atualizado')
            ->set('status', 'client')
            ->call('save');

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Cliente Atualizado',
            'status' => 'client',
            'company_id' => $user->company_id,
        ]);
    }

    public function test_update_requires_valid_fields(): void
    {
        $user = $this->createUserWithPermissions(['clients.view', 'clients.update']);
        $client = Client::factory()->create([
            'company_id' => $user->company_id,
        ]);

        $this->actingAs($user);

        Livewire::test(ClientsIndex::class)
            ->call('openEditModal', $client->id)
            ->set('name', '')
            ->set('status', 'invalid')
            ->call('save')
            ->assertHasErrors(['name', 'status']);
    }

    public function test_user_without_delete_permission_cannot_delete_client(): void
    {
        $user = $this->createUserWithPermissions(['clients.view']);
        $client = Client::factory()->create([
            'company_id' => $user->company_id,
        ]);

        $this->actingAs($user);

        Livewire::test(ClientsIndex::class)
            ->call('delete', $client->id)
            ->assertStatus(403);
    }

    public function test_user_with_delete_permission_can_delete_client(): void
    {
        $user = $this->createUserWithPermissions(['clients.view', 'clients.delete']);
        $client = Client::factory()->create([
            'company_id' => $user->company_id,
        ]);

        $this->actingAs($user);

        Livewire::test(ClientsIndex::class)
            ->call('delete', $client->id);

        $this->assertSoftDeleted('clients', [
            'id' => $client->id,
        ]);
    }

    public function test_clients_list_is_scoped_to_company(): void
    {
        $user = $this->createUserWithPermissions(['clients.view']);
        Client::factory()->create([
            'company_id' => $user->company_id,
            'name' => 'Cliente Alpha',
        ]);
        Client::factory()->create([
            'name' => 'Cliente Beta',
        ]);

        $this->actingAs($user);

        Livewire::test(ClientsIndex::class)
            ->assertSee('Cliente Alpha')
            ->assertDontSee('Cliente Beta');
    }

    public function test_show_client_requires_view_permission_and_scope(): void
    {
        $user = $this->createUserWithPermissions(['clients.view']);
        $client = Client::factory()->create([
            'company_id' => $user->company_id,
        ]);

        $this->actingAs($user)
            ->get("/clients/{$client->id}")
            ->assertOk();
    }

    public function test_show_client_for_other_company_returns_404(): void
    {
        $user = $this->createUserWithPermissions(['clients.view']);
        $otherClient = Client::factory()->create();

        $this->actingAs($user)
            ->get("/clients/{$otherClient->id}")
            ->assertNotFound();
    }

    public function test_search_and_status_filters_work(): void
    {
        $user = $this->createUserWithPermissions(['clients.view']);
        Client::factory()->create([
            'company_id' => $user->company_id,
            'name' => 'Alice Teste',
            'status' => 'lead',
        ]);
        Client::factory()->create([
            'company_id' => $user->company_id,
            'name' => 'Bruno Cliente',
            'status' => 'client',
        ]);

        $this->actingAs($user);

        Livewire::test(ClientsIndex::class)
            ->set('search', 'Alice')
            ->assertSee('Alice Teste')
            ->assertDontSee('Bruno Cliente')
            ->set('search', '')
            ->set('statusFilter', 'client')
            ->assertSee('Bruno Cliente')
            ->assertDontSee('Alice Teste');
    }

    public function test_clear_filters_restores_default_listing(): void
    {
        $user = $this->createUserWithPermissions(['clients.view']);

        Client::factory()->create([
            'company_id' => $user->company_id,
            'name' => 'Alice Teste',
            'status' => 'lead',
        ]);
        Client::factory()->create([
            'company_id' => $user->company_id,
            'name' => 'Bruno Cliente',
            'status' => 'client',
        ]);

        $this->actingAs($user);

        Livewire::test(ClientsIndex::class)
            ->set('search', 'Alice')
            ->set('statusFilter', 'lead')
            ->assertSee('Alice Teste')
            ->assertDontSee('Bruno Cliente')
            ->call('clearFilters')
            ->assertSee('Alice Teste')
            ->assertSee('Bruno Cliente');
    }

}
