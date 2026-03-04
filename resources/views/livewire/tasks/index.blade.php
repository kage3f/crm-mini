<div>
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Filtrar tarefas..." class="input pl-10 w-64">
            </div>

            <select wire:model.live="statusFilter" class="input w-44">
                <option value="">Todos os status</option>
                <option value="pending">Pendentes</option>
                <option value="in_progress">Em andamento</option>
                <option value="done">Concluídas</option>
            </select>

            <select wire:model.live="dateFilter" class="input w-44">
                <option value="">Qualquer data</option>
                <option value="today">Para hoje</option>
                <option value="week">Esta semana</option>
                <option value="overdue">Atrasadas</option>
            </select>
        </div>

        <button wire:click="openCreateModal" class="btn-primary">
            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nova Tarefa
        </button>
    </div>

    {{-- Overdue Alert --}}
    @if($overdueCount > 0)
    <div class="alert-warning mb-6">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Existem <strong>{{ $overdueCount }} tarefas atrasadas</strong> precisando de atenção.</span>
    </div>
    @endif

    {{-- Tasks List --}}
    <div class="card overflow-hidden">
        <div class="divide-y divide-slate-100">
            @forelse($tasks as $task)
            <div class="p-5 flex items-center gap-4 hover:bg-slate-50 transition-colors group">
                {{-- Checkbox --}}
                <div class="shrink-0">
                    <button wire:click="markDone({{ $task->id }})"
                        class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all 
                                {{ $task->status === 'done' ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-300 hover:border-brand-500' }}"
                        {{ $task->status === 'done' ? 'disabled' : '' }}>
                        @if($task->status === 'done')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                        @endif
                    </button>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3">
                        <h4 class="font-semibold text-sm {{ $task->status === 'done' ? 'text-slate-400 line-through' : 'text-slate-900 font-bold' }}">
                            {{ $task->title }}
                        </h4>
                        <span class="{{ $task->status_color }}">
                            {{ $task->status_label }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 mt-1.5 text-xs text-slate-500">
                        @if($task->client)
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {{ $task->client->name }}
                        </span>
                        @endif
                        @if($task->opportunity)
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                            </svg>
                            {{ $task->opportunity->title }}
                        </span>
                        @endif
                        @if($task->due_date)
                        <span class="flex items-center gap-1 {{ $task->isOverdue() ? 'text-red-500 font-bold' : '' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $task->due_date->format('d/m/Y') }}
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="shrink-0 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button wire:click="openEditModal({{ $task->id }})" class="p-2 text-slate-400 hover:text-brand-600 transition-colors">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </button>
                    <button wire:click="delete({{ $task->id }})" wire:confirm="Excluir esta tarefa?" class="p-2 text-slate-400 hover:text-red-600 transition-colors">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
            @empty
            <div class="py-20 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="text-slate-900 font-medium font-bold">Tudo em dia!</h3>
                <p class="text-slate-500 text-sm mt-1">Nenhuma tarefa pendente no momento.</p>
            </div>
            @endforelse
        </div>
        @if($tasks->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50">
            {{ $tasks->links() }}
        </div>
        @endif
    </div>

    {{-- Task Modal --}}
    @if($showModal)
    <div class="modal-overlay">
        <div class="modal-panel max-w-lg" wire:click.away="closeModal">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900">{{ $isEditing ? 'Editar Tarefa' : 'Nova Tarefa' }}</h3>
                <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form wire:submit.prevent="save" class="p-6 space-y-4">
                <div>
                    <label class="label">Título da tarefa *</label>
                    <input wire:model="title" type="text" class="input @error('title') input-error @enderror" placeholder="Ex: Ligar para confirmar proposta">
                    @error('title') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Status</label>
                        <select wire:model="status" class="input">
                            <option value="pending">Pendente</option>
                            <option value="in_progress">Em andamento</option>
                            <option value="done">Concluída</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Prazo</label>
                        <input wire:model="due_date" type="date" class="input">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Cliente (opcional)</label>
                        <select wire:model="client_id" class="input">
                            <option value="">Nenhum cliente</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Oportunidade (opcional)</label>
                        <select wire:model="opportunity_id" class="input">
                            <option value="">Nenhuma oportunidade</option>
                            @foreach($opportunities as $opp)
                            <option value="{{ $opp->id }}">{{ $opp->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="label">Responsável</label>
                    <select wire:model="assigned_to" class="input">
                        <option value="">Ninguém (sem atribuição)</option>
                        @foreach($teamMembers as $member)
                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="label">Descrição / Notas</label>
                    <textarea wire:model="description" rows="3" class="input" placeholder="Detalhes adicionais sobre o que deve ser feito..."></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" wire:click="closeModal" class="btn-secondary">Cancelar</button>
                    <button type="submit" class="btn-primary">Salvar Tarefa</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>