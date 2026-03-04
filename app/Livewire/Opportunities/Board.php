<?php

namespace App\Livewire\Opportunities;

use App\Actions\Opportunities\MoveOpportunityAction;
use App\Models\Client;
use App\Models\Opportunity;
use App\Models\OpportunityStage;
use Livewire\Component;

class Board extends Component
{
    // Modal
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    // Form fields
    public string $title             = '';
    public string $client_id         = '';
    public string $stage_id          = '';
    public string $value             = '0';
    public string $expected_close_date = '';
    public string $notes             = '';

    protected array $rules = [
        'title'               => 'required|string|min:2|max:255',
        'client_id'           => 'nullable|integer',
        'stage_id'            => 'required|integer',
        'value'               => 'required|numeric|min:0',
        'expected_close_date' => 'nullable|date',
        'notes'               => 'nullable|string|max:5000',
    ];

    public function move(int $opportunityId, int $stageId, MoveOpportunityAction $action): void
    {
        $opportunity = Opportunity::findOrFail($opportunityId);
        $action->execute($opportunity, $stageId);
    }

    public function openCreateModal(?int $stageId = null): void
    {
        $this->resetForm();
        $this->stage_id  = (string) ($stageId ?? '');
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function openEditModal(int $id): void
    {
        $opp = Opportunity::findOrFail($id);
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

    public function save(): void
    {
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

        if ($this->isEditing) {
            Opportunity::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Oportunidade atualizada!');
        } else {
            Opportunity::create($data);
            session()->flash('success', 'Oportunidade criada!');
        }

        $this->closeModal();
    }

    public function delete(int $id): void
    {
        Opportunity::findOrFail($id)->delete();
        session()->flash('success', 'Oportunidade removida.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->title = $this->client_id = $this->stage_id = $this->notes = $this->expected_close_date = '';
        $this->value = '0';
        $this->editingId = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        // Global scope from HasCompany handles company_id isolation
        $stages = OpportunityStage::orderBy('order')
            ->with(['opportunities' => fn($q) => $q->with('client')->orderByDesc('value')])
            ->get();

        $clients = Client::orderBy('name')->get(['id', 'name']);

        $totalEstimated = $stages->flatMap->opportunities
            ->filter(fn($opp) => !in_array($opp->stage?->name, ['Fechado - Ganho', 'Fechado - Perdido']))
            ->sum('value');

        return view('livewire.opportunities.board', compact('stages', 'clients', 'totalEstimated'))
            ->layout('layouts.app', ['title' => 'Pipeline de Oportunidades']);
    }
}
