<?php

namespace App\Livewire\Tasks;

use App\Models\Client;
use App\Models\Opportunity;
use App\Models\Task;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Filters
    public string $statusFilter = '';
    public string $dateFilter   = '';
    public string $search       = '';

    // Modal
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    // Form
    public string $title          = '';
    public string $description    = '';
    public string $client_id      = '';
    public string $opportunity_id = '';
    public string $assigned_to    = '';
    public string $status         = 'pending';
    public string $due_date       = '';

    protected array $rules = [
        'title'          => 'required|string|min:2|max:255',
        'description'    => 'nullable|string|max:5000',
        'client_id'      => 'nullable|integer',
        'opportunity_id' => 'nullable|integer',
        'assigned_to'    => 'nullable|integer',
        'status'         => 'required|in:pending,in_progress,done',
        'due_date'       => 'nullable|date',
    ];

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function openEditModal(int $id): void
    {
        $task = Task::findOrFail($id);
        $this->editingId      = $id;
        $this->title          = $task->title;
        $this->description    = $task->description ?? '';
        $this->client_id      = (string) ($task->client_id ?? '');
        $this->opportunity_id = (string) ($task->opportunity_id ?? '');
        $this->assigned_to    = (string) ($task->assigned_to ?? '');
        $this->status         = $task->status;
        $this->due_date       = $task->due_date?->format('Y-m-d') ?? '';
        $this->isEditing      = true;
        $this->showModal      = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title'          => $this->title,
            'description'    => $this->description ?: null,
            'client_id'      => $this->client_id ?: null,
            'opportunity_id' => $this->opportunity_id ?: null,
            'assigned_to'    => $this->assigned_to ?: null,
            'status'         => $this->status,
            'due_date'       => $this->due_date ?: null,
            'created_by'     => auth()->id(),
        ];

        if ($this->isEditing) {
            Task::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Tarefa atualizada!');
        } else {
            Task::create($data);
            session()->flash('success', 'Tarefa criada!');
        }

        $this->closeModal();
    }

    public function markDone(int $id): void
    {
        Task::findOrFail($id)->update(['status' => 'done']);
        session()->flash('success', 'Tarefa marcada como concluída!');
    }

    public function delete(int $id): void
    {
        Task::findOrFail($id)->delete();
        session()->flash('success', 'Tarefa removida.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->title = $this->description = $this->client_id = $this->opportunity_id = $this->assigned_to = $this->due_date = '';
        $this->status = 'pending';
        $this->editingId = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        $tasks = Task::query()
            ->with(['client', 'opportunity'])
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->dateFilter === 'overdue', fn($q) => $q->where('due_date', '<', today())->where('status', '!=', 'done'))
            ->when($this->dateFilter === 'today', fn($q) => $q->whereDate('due_date', today()))
            ->when($this->dateFilter === 'week', fn($q) => $q->whereBetween('due_date', [today(), today()->addWeek()]))
            ->orderByRaw("
                CASE status
                    WHEN 'pending' THEN 1
                    WHEN 'in_progress' THEN 2
                    WHEN 'done' THEN 3
                    ELSE 4
                END
            ")
            ->orderBy('due_date')
            ->paginate(20);

        $overdueCount = Task::where('status', '!=', 'done')
            ->whereNotNull('due_date')
            ->where('due_date', '<', today())
            ->count();

        $companyId = auth()->user()->company_id;
        $teamMembers = User::where('company_id', $companyId)->get(['id', 'name']);
        $clients = Client::orderBy('name')->get(['id', 'name']);
        $opportunities = Opportunity::orderBy('title')->get(['id', 'title']);

        return view('livewire.tasks.index', compact('tasks', 'overdueCount', 'teamMembers', 'clients', 'opportunities'))
            ->layout('layouts.app', ['title' => 'Tarefas']);
    }
}
