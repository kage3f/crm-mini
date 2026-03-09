<?php

namespace Tests\Feature\Settings;

use App\Livewire\Settings\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\Concerns\CreatesUsersWithPermissions;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase, CreatesUsersWithPermissions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionsSeeder::class);
    }

    public function test_guest_is_redirected_from_team_page(): void
    {
        $this->get('/settings/team')->assertRedirect('/login');
    }

    public function test_user_without_manage_team_permission_cannot_access_team_page(): void
    {
        $user = $this->createUserWithPermissions([]);

        $this->actingAs($user)
            ->get('/settings/team')
            ->assertForbidden();
    }

    public function test_user_with_manage_team_permission_can_access_team_page(): void
    {
        $user = $this->createUserWithPermissions(['manage-team']);

        $this->actingAs($user)
            ->get('/settings/team')
            ->assertOk();
    }

    public function test_admin_can_access_team_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/settings/team')
            ->assertOk();
    }

    public function test_invite_requires_manage_team_permission(): void
    {
        $user = $this->createUserWithPermissions([]);

        $this->actingAs($user);

        Livewire::test(Team::class)
            ->set('email', 'novo@exemplo.com')
            ->set('role', 'member')
            ->call('invite')
            ->assertStatus(403);
    }

    public function test_user_with_manage_team_permission_can_invite_member(): void
    {
        Notification::fake();

        $user = $this->createUserWithPermissions(['manage-team']);

        $this->actingAs($user);

        Livewire::test(Team::class)
            ->set('email', 'convidado@exemplo.com')
            ->set('role', 'member')
            ->call('invite');

        $this->assertDatabaseHas('team_invitations', [
            'company_id' => $user->company_id,
            'email' => 'convidado@exemplo.com',
            'role' => 'member',
        ]);
    }

    public function test_invite_fails_when_user_already_in_company(): void
    {
        Notification::fake();

        $user = $this->createUserWithPermissions(['manage-team']);
        User::factory()->create([
            'company_id' => $user->company_id,
            'email' => 'ja@exemplo.com',
        ]);

        $this->actingAs($user);

        Livewire::test(Team::class)
            ->set('email', 'ja@exemplo.com')
            ->set('role', 'member')
            ->call('invite')
            ->assertHasErrors(['email']);

        $this->assertDatabaseMissing('team_invitations', [
            'company_id' => $user->company_id,
            'email' => 'ja@exemplo.com',
        ]);
    }

    public function test_cancel_invitation_requires_manage_team_permission(): void
    {
        $user = $this->createUserWithPermissions([]);
        $invitation = TeamInvitation::factory()->create([
            'company_id' => $user->company_id,
        ]);

        $this->actingAs($user);

        Livewire::test(Team::class)
            ->call('cancelInvitation', $invitation->id)
            ->assertStatus(403);
    }

    public function test_user_with_manage_team_permission_can_cancel_invitation(): void
    {
        $user = $this->createUserWithPermissions(['manage-team']);
        $invitation = TeamInvitation::factory()->create([
            'company_id' => $user->company_id,
        ]);

        $this->actingAs($user);

        Livewire::test(Team::class)
            ->call('cancelInvitation', $invitation->id);

        $this->assertDatabaseMissing('team_invitations', [
            'id' => $invitation->id,
        ]);
    }

    public function test_remove_member_requires_admin_role(): void
    {
        $user = $this->createUserWithPermissions(['manage-team']);
        $member = User::factory()->create([
            'company_id' => $user->company_id,
        ]);

        $this->actingAs($user);

        Livewire::test(Team::class)
            ->call('removeMember', $member->id)
            ->assertStatus(403);
    }

    public function test_admin_can_remove_member_from_company(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $member = User::factory()->create([
            'company_id' => $admin->company_id,
        ]);

        $this->actingAs($admin);

        Livewire::test(Team::class)
            ->call('removeMember', $member->id);

        $this->assertDatabaseMissing('users', [
            'id' => $member->id,
        ]);
    }
}
