<?php

namespace Tests\Feature\Settings;

use App\Livewire\Settings\Permissions as PermissionsPage;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;
use Tests\Concerns\CreatesUsersWithPermissions;
use Tests\TestCase;

class PermissionsTest extends TestCase
{
    use RefreshDatabase, CreatesUsersWithPermissions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionsSeeder::class);
    }

    public function test_guest_is_redirected_from_permissions_page(): void
    {
        $this->get('/settings/permissions')->assertRedirect('/login');
    }

    public function test_user_without_permission_cannot_access_permissions_page(): void
    {
        $user = $this->createUserWithPermissions([]);

        $this->actingAs($user)
            ->get('/settings/permissions')
            ->assertForbidden();
    }

    public function test_user_with_manage_permissions_can_access_permissions_page(): void
    {
        $user = $this->createUserWithPermissions(['manage-permissions']);

        $this->actingAs($user)
            ->get('/settings/permissions')
            ->assertOk();
    }

    public function test_admin_can_access_permissions_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get('/settings/permissions')
            ->assertOk();
    }

    public function test_toggle_permission_grants_and_revokes_permission(): void
    {
        $manager = $this->createUserWithPermissions(['manage-permissions']);
        $member = $this->createUserWithPermissions([]);
        $member->update(['company_id' => $manager->company_id]);

        $this->actingAs($manager);

        Livewire::test(PermissionsPage::class)
            ->call('togglePermission', $member->id, 'clients.view');

        $this->assertTrue($member->fresh()->hasPermissionTo('clients.view'));

        Livewire::test(PermissionsPage::class)
            ->call('togglePermission', $member->id, 'clients.view');

        $this->assertFalse($member->fresh()->hasPermissionTo('clients.view'));
    }

    public function test_toggle_permission_does_nothing_for_admin_target(): void
    {
        $manager = $this->createUserWithPermissions(['manage-permissions']);
        $admin = User::factory()->create(['company_id' => $manager->company_id]);
        $admin->assignRole('admin');

        $this->actingAs($manager);

        Livewire::test(PermissionsPage::class)
            ->call('togglePermission', $admin->id, 'clients.view');

        $this->assertTrue($admin->fresh()->hasPermissionTo('clients.view'));
    }

    public function test_toggle_permission_for_other_company_returns_404(): void
    {
        $manager = $this->createUserWithPermissions(['manage-permissions']);
        $other = User::factory()->create();

        $this->actingAs($manager);

        $this->expectException(ModelNotFoundException::class);

        Livewire::test(PermissionsPage::class)
            ->call('togglePermission', $other->id, 'clients.view');
    }
}
