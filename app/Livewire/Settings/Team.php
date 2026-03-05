<?php

namespace App\Livewire\Settings;

use App\Actions\Settings\InviteTeamMemberAction;
use App\Models\TeamInvitation;
use App\Models\User;
use Livewire\Component;

class Team extends Component
{
    public string $email = '';
    public string $role  = 'member';

    protected array $rules = [
        'email' => 'required|email',
        'role'  => 'required|in:admin,member',
    ];

    public function invite(InviteTeamMemberAction $action): void
    {
        $this->validate();

        $companyId = auth()->user()->company_id;

        if (User::where('company_id', $companyId)->where('email', $this->email)->exists()) {
            $this->addError('email', 'Este usuário já faz parte da equipe.');
            return;
        }

        $action->execute($companyId, $this->email, $this->role);

        $this->email = '';
        session()->flash('success', 'Convite enviado com sucesso!');
    }

    public function removeMember(int $userId): void
    {
        abort_unless(auth()->user()?->hasRole('admin'), 403);

        User::where('id', $userId)
            ->where('company_id', auth()->user()->company_id)
            ->where('id', '!=', auth()->id())
            ->delete();
        session()->flash('success', 'Membro removido da equipe.');
    }

    public function cancelInvitation(int $id): void
    {
        TeamInvitation::where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->delete();
        session()->flash('success', 'Convite cancelado.');
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;

        $members = User::where('company_id', $companyId)->get();
        $pendingInvitations = TeamInvitation::where('company_id', $companyId)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->get();

        return view('livewire.settings.team', compact('members', 'pendingInvitations'))
            ->layout('layouts.app', ['title' => 'Equipe']);
    }
}
