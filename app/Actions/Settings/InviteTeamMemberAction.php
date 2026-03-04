<?php

namespace App\Actions\Settings;

use App\Models\TeamInvitation;
use App\Notifications\TeamInvitationNotification;
use Illuminate\Support\Str;

class InviteTeamMemberAction
{
    public function execute(int $companyId, string $email, string $role = 'member'): TeamInvitation
    {
        // Cancel any pending invitation
        TeamInvitation::where('company_id', $companyId)
            ->where('email', $email)
            ->whereNull('accepted_at')
            ->delete();

        $invitation = TeamInvitation::create([
            'company_id' => $companyId,
            'email'      => $email,
            'token'      => Str::random(64),
            'role'       => $role,
            'expires_at' => now()->addDays(7),
        ]);

        \Illuminate\Support\Facades\Notification::route('mail', $email)
            ->notify(new TeamInvitationNotification($invitation));

        return $invitation;
    }
}
