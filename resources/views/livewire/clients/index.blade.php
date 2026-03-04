<div>
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar clientes..." class="input pl-10 w-64">
            </div>
            <select wire:model.live="statusFilter" class="input w-40">
                <option value="">Todos os status</option>
                <option value="lead">Lead</option>
                <option value="client">Cliente</option>
                <option value="inactive">Inativo</option>
            </select>
        </div>
        <button wire:click="openCreateModal" class="btn-primary">
            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Novo Cliente
        </button>
    </div>

    {{-- Clients Table --}}
    <div class="card overflow-hidden">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nome / Empresa</th>
                        <th>Contato</th>
                        <th>Status</th>
                        <th>Criado em</th>
                        <th class="w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($clients as $client)
                    <tr>
                        <td>
                            <a href="{{ route('clients.show', $client) }}" class="font-semibold text-slate-900 hover:text-brand-600">
                                {{ $client->name }}
                            </a>
                            @if($client->company)
                            <p class="text-xs text-slate-500 mt-0.5">{{ $client->company }}</p>
                            @endif
                        </td>
                        <td>
                            <p class="text-sm text-slate-700">{{ $client->email ?? '-' }}</p>
                            <p class="text-xs text-slate-500">{{ $client->phone ?? '-' }}</p>
                        </td>
                        <td>
                            <span class="{{ $client->status_color }}">
                                {{ $client->status_label }}
                            </span>
                        </td>
                        <td class="text-slate-500 text-sm">
                            {{ $client->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="openEditModal({{ $client->id }})" class="p-1.5 text-slate-400 hover:text-brand-600 transition-colors" title="Editar">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $client->id }})" wire:confirm="Tem certeza que deseja excluir este cliente?" class="p-1.5 text-slate-400 hover:text-red-600 transition-colors" title="Excluir">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-slate-900 font-medium">Nenhum cliente encontrado</h3>
                                <p class="text-slate-500 text-sm mt-1">Tente ajustar seus filtros ou cadastre um novo cliente.</p>
                            </div>
                        </td>
                        @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
            {{ $clients->links() }}
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="modal-overlay">
        <div class="modal-panel max-w-2xl" wire:click.away="closeModal">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900">{{ $isEditing ? 'Editar Cliente' : 'Novo Cliente' }}</h3>
                <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form wire:submit.prevent="save" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 sm:col-span-1">
                        <label class="label">Nome completo *</label>
                        <input wire:model="name" type="text" class="input @error('name') input-error @enderror" placeholder="Ex: João Silva">
                        @error('name') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label class="label">Empresa</label>
                        <input wire:model="company" type="text" class="input @error('company') input-error @enderror" placeholder="Ex: Acme Corp">
                        @error('company') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 sm:col-span-1">
                        <label class="label">Email</label>
                        <input wire:model="email" type="email" class="input @error('email') input-error @enderror" placeholder="joao@example.com">
                        @error('email') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label class="label">Telefone</label>
                        <input wire:model="phone" type="text" class="input @error('phone') input-error @enderror" placeholder="(11) 99999-9999">
                        @error('phone') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="label">Status</label>
                    <select wire:model="status" class="input">
                        <option value="lead">Lead</option>
                        <option value="client">Cliente</option>
                        <option value="inactive">Inativo</option>
                    </select>
                    @error('status') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Observações</label>
                    <textarea wire:model="notes" rows="4" class="input" placeholder="Observações internas sobre o cliente..."></textarea>
                    @error('notes') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" wire:click="closeModal" class="btn-secondary">Cancelar</button>
                    <button type="submit" class="btn-primary">
                        {{ $isEditing ? 'Salvar Alterações' : 'Criar Cliente' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
