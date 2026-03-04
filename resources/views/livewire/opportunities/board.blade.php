<div class="h-full flex flex-col">
    <div class="flex items-center justify-between mb-6 shrink-0">
        <div>
            <h3 class="text-sm text-slate-500 font-medium">Pipeline Comercial</h3>
            <p class="text-xs text-slate-400 mt-0.5">Total previsto em aberto: <strong>R$ {{ number_format($totalEstimated, 2, ',', '.') }}</strong></p>
        </div>
        <button wire:click="openCreateModal" class="btn-primary">
            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nova Oportunidade
        </button>
    </div>

    {{-- Kanban Board --}}
    <div class="flex-1 overflow-x-auto pb-6 -mx-6 px-6">
        <div class="flex gap-4 h-full min-h-[600px] items-start">
            @foreach($stages as $stage)
            <div class="kanban-col flex flex-col" data-stage-id="{{ $stage->id }}">
                <div class="flex items-center justify-between mb-4 px-1 shrink-0">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $stage->color }}"></div>
                        <h4 class="font-bold text-slate-700 text-sm uppercase tracking-wider">{{ $stage->name }}</h4>
                    </div>
                    <span class="text-[10px] font-bold bg-slate-200 text-slate-500 px-1.5 py-0.5 rounded-full">
                        {{ $stage->opportunities->count() }}
                    </span>
                </div>

                <div class="flex-1 space-y-3 kanban-list" data-stage="{{ $stage->id }}" id="stage-{{ $stage->id }}">
                    @foreach($stage->opportunities as $opp)
                    <div class="kanban-card group relative" data-id="{{ $opp->id }}" wire:click="openEditModal({{ $opp->id }})">
                        <div class="mb-2">
                            <h5 class="font-bold text-slate-900 text-sm group-hover:text-brand-600 transition-colors">{{ $opp->title }}</h5>
                            @if($opp->client)
                            <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $opp->client->name }}</p>
                            @endif
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-50">
                            <span class="text-sm font-bold text-slate-800">{{ $opp->formatted_value }}</span>
                            @if($opp->expected_close_date)
                            <div class="flex items-center gap-1 text-[10px] {{ $opp->expected_close_date->isPast() ? 'text-red-500' : 'text-slate-400' }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $opp->expected_close_date->format('d/m') }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    {{-- Drop zone padding --}}
                    <div class="h-12 pointer-events-none"></div>
                </div>

                <button wire:click="openCreateModal({{ $stage->id }})" class="mt-4 w-full py-2 border-2 border-dashed border-slate-200 rounded-lg text-slate-400 text-xs font-bold hover:border-slate-300 hover:text-slate-500 transition-all flex items-center justify-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Adicionar
                </button>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Opportunity Modal --}}
    @if($showModal)
    <div class="modal-overlay">
        <div class="modal-panel max-w-lg" wire:click.away="closeModal">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900">{{ $isEditing ? 'Editar Oportunidade' : 'Nova Oportunidade' }}</h3>
                <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form wire:submit.prevent="save" class="p-6 space-y-4">
                <div>
                    <label class="label">Título da negociação *</label>
                    <input wire:model="title" type="text" class="input @error('title') input-error @enderror" placeholder="Ex: Contrato de Manutenção Anual">
                    @error('title') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Cliente</label>
                        <select wire:model="client_id" class="input">
                            <option value="">Nenhum cliente</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Estágio *</label>
                        <select wire:model="stage_id" class="input">
                            @foreach($stages as $stage)
                            <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Valor (R$)</label>
                        <input wire:model="value" type="number" step="0.01" class="input @error('value') input-error @enderror">
                        @error('value') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label">Previsão de fechamento</label>
                        <input wire:model="expected_close_date" type="date" class="input">
                    </div>
                </div>

                <div>
                    <label class="label">Notas / Detalhes</label>
                    <textarea wire:model="notes" rows="3" class="input"></textarea>
                </div>

                <div class="pt-4 flex items-center justify-between">
                    @if($isEditing)
                    <button type="button" wire:click="delete({{ $editingId }})" wire:confirm="Excluir esta oportunidade?" class="btn-ghost text-red-600">Excluir</button>
                    @else
                    <div></div>
                    @endif
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="closeModal" class="btn-secondary">Cancelar</button>
                        <button type="submit" class="btn-primary">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Drag & Drop Script --}}
    @script
    <script>
        // Inicializa direto — sem esperar evento nenhum
        function initSortable() {
            document.querySelectorAll('.kanban-list').forEach(el => {
                if (el._sortable) el._sortable.destroy(); // evita duplicar ao re-render

                el._sortable = new Sortable(el, {
                    group: 'kanban',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    onEnd(evt) {
                        if (evt.from === evt.to) return;
                        $wire.move(
                            evt.item.dataset.id,
                            evt.to.dataset.stage
                        );
                    }
                });
            });
        }

        initSortable();

        // Reinicializa após cada re-render do Livewire
        $wire.on('kanban-refreshed', initSortable);
    </script>
    @endscript
</div>
