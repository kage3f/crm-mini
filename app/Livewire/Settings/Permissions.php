<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Livewire\Component;

class Permissions extends Component
{
    public array $permissionGroups = [];

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('manage-permissions'), 403);

        $this->permissionGroups = [
            'Clientes' => [
                'clients.view' => 'Ver clientes',
                'clients.create' => 'Criar clientes',
                'clients.update' => 'Editar clientes',
                'clients.delete' => 'Excluir clientes',
            ],
            'Funil de Vendas' => [
                'opportunities.view' => 'Ver oportunidades',
                'opportunities.create' => 'Criar oportunidades',
                'opportunities.update' => 'Editar/mover oportunidades',
                'opportunities.delete' => 'Excluir oportunidades',
            ],
            'Tarefas' => [
                'tasks.view' => 'Ver tarefas',
                'tasks.create' => 'Criar tarefas',
                'tasks.update' => 'Editar/concluir tarefas',
                'tasks.delete' => 'Excluir tarefas',
                'tasks.assign' => 'Atribuir tarefas',
            ],
            'Dados' => [
                'export-data' => 'Exportar dados',
            ],
            'Financeiro' => [
                'view-billing' => 'Ver cobranca',
            ],
            'Equipe' => [
                'manage-team' => 'Convidar/remover membros',
                'manage-permissions' => 'Gerenciar permissoes',
            ],
        ];
    }

    public function togglePermission(int $userId, string $permission): void
    {
        abort_unless(auth()->user()?->can('manage-permissions'), 403);

        $user = User::where('company_id', auth()->user()->company_id)
            ->where('id', $userId)
            ->firstOrFail();

        if ($user->hasRole('admin')) {
            return;
        }

        if ($user->hasPermissionTo($permission)) {
            $user->revokePermissionTo($permission);
        } else {
            $user->givePermissionTo($permission);
        }
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;
        $members = User::where('company_id', $companyId)
            ->with('permissions')
            ->orderBy('name')
            ->get();

        return view('livewire.settings.permissions', [
            'members' => $members,
        ])->layout('layouts.app', ['title' => 'Permissoes']);
    }
}
