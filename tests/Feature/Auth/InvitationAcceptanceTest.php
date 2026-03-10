<?php

namespace Tests\Feature\Auth;

use App\Models\TeamInvitation;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionsSeeder::class);
    }

    public function test_accepting_team_invitation_marks_email_as_verified(): void
    {
        $invitation = TeamInvitation::factory()->create([
            'email' => 'convite@exemplo.com',
            'role' => 'member',
        ]);

        $response = $this->post(route('invitations.store', $invitation->token), [
            'name' => 'Usuário Convidado',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $user = User::where('email', 'convite@exemplo.com')->first();

        $response->assertRedirect(route('dashboard'));
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertNotNull($invitation->fresh()->accepted_at);
    }
}
