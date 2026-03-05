<?php

namespace App\Livewire\Opportunities;

use App\Actions\Opportunities\DeleteOpportunityAction;
use App\Actions\Opportunities\MoveOpportunityAction;
use App\Actions\Opportunities\SaveOpportunityAction;
use App\Models\Client;
use App\Models\Opportunity;
use App\Models\OpportunityStage;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Board extends Component
{
    // Modal
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    #[Rule('required|string|min:2|max:255')]
    public string $title = '';

    #[Rule('nullable|integer')]
    public string $client_id = '';

    #[Rule('required|integer')]
    public string $stage_id = '';

    #[Rule('required|numeric|min:0')]
    public string $value = '0';

    #[Rule('nullable|date')]
    public string $expected_close_date = '';

    #[Rule('nullable|string|max:5000')]
    public string $notes = '';

    public string $dateFrom = '';
    public string $dateTo = '';


    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
    }


    public function move(int $opportunityId, int $stageId, MoveOpportunityAction $action): void
    {
        $opportunity = Opportunity::findOrFail($opportunityId);
        $this->authorize('update', $opportunity);
        $action->execute($opportunity, $stageId);
    }

    public function save(SaveOpportunityAction $action): void
    {
        $this->isEditing
            ? $this->authorize('update', Opportunity::findOrFail($this->editingId))
            : $this->authorize('create', Opportunity::class);

        $this->validate();

        $data = [
            'title'               => $this->title,
            'client_id'           => $this->client_id ?: null,
            'stage_id'            => (int) $this->stage_id,
            'value'               => (float) $this->value,
            'expected_close_date' => $this->expected_close_date ?: null,
            'notes'               => $this->notes ?: null,
            'created_by'          => auth()->id(),
        ];

        $existing = $this->isEditing ? Opportunity::findOrFail($this->editingId) : null;
        $action->execute($data, $existing);

        session()->flash('success', $this->isEditing ? 'Oportunidade atualizada.' : 'Oportunidade criada.');
        $this->closeModal();
    }

    public function delete(int $id, DeleteOpportunityAction $action): void
    {
        $opportunity = Opportunity::findOrFail($id);
        $this->authorize('delete', $opportunity);

        $action->execute($opportunity);
        session()->flash('success', 'Oportunidade removida.');
    }

    public function openCreateModal(?int $stageId = null): void
    {
        $this->authorize('create', Opportunity::class);

        $this->resetForm();
        $this->stage_id  = (string) ($stageId ?? '');
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function openEditModal(int $id): void
    {
        $opp = Opportunity::findOrFail($id);
        $this->authorize('update', $opp);

        $this->editingId           = $id;
        $this->title               = $opp->title;
        $this->client_id           = (string) ($opp->client_id ?? '');
        $this->stage_id            = (string) $opp->stage_id;
        $this->value               = (string) $opp->value;
        $this->expected_close_date = $opp->expected_close_date?->format('Y-m-d') ?? '';
        $this->notes               = $opp->notes ?? '';
        $this->isEditing           = true;
        $this->showModal           = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function render()
    {
        $this->authorize('viewAny', Opportunity::class);

        $stages = OpportunityStage::orderBy('order')
            ->with([
                'opportunities' => fn ($q) => $q
                    ->select(['id', 'title', 'client_id', 'stage_id', 'value', 'expected_close_date'])
                    ->with('client:id,name')
                    ->when($this->dateFrom, fn ($q) =>
                    $q->whereDate('expected_close_date', '>=', $this->dateFrom)
                    )
                    ->when($this->dateTo, fn ($q) =>
                    $q->whereDate('expected_close_date', '<=', $this->dateTo)
                    )
                    ->orderByDesc('value')
                    ->limit(100),
            ])
            ->get();

        $clients = Client::orderBy('name')->limit(500)->get(['id', 'name']);

        $closedIds = OpportunityStage::whereIn('name', ['Fechado - Ganho', 'Fechado - Perdido'])
            ->pluck('id');

        $totalEstimated = $stages
            ->flatMap->opportunities
            ->filter(fn ($opp) => ! $closedIds->contains($opp->stage_id))
            ->sum('value');

        return view('livewire.opportunities.board', compact('stages', 'clients', 'totalEstimated'))
            ->layout('layouts.app', ['title' => 'Pipeline de Oportunidades']);
    }

    // -------------------------------------------------------------------------

    private function resetForm(): void
    {
        $this->title               = '';
        $this->client_id           = '';
        $this->stage_id            = '';
        $this->notes               = '';
        $this->expected_close_date = '';
        $this->value               = '0';
        $this->editingId           = null;
        $this->resetErrorBag();
    }

    public function refresh(): void
    {
        $this->dispatch('kanban-refreshed');
    }

    public function clearFilters(): void
    {
        $this->dateFrom = '';
        $this->dateTo   = '';
        $this->dispatch('filters-cleared');
    }
}
