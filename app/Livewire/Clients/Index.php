<?php

namespace App\Livewire\Clients;

use App\Actions\Clients\CreateClientAction;
use App\Actions\Clients\UpdateClientAction;
use App\Models\Subscription;
use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public string $statusFilter = '';

    // Modal
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    // Form
    public string $name    = '';
    public string $email   = '';
    public string $phone   = '';
    public string $company = '';
    public string $status  = 'lead';
    public string $notes   = '';

    protected array $rules = [
        'name'    => 'required|string|min:2|max:255',
        'email'   => 'nullable|email|max:255',
        'phone'   => 'nullable|string|max:30',
        'company' => 'nullable|string|max:255',
        'status'  => 'required|in:lead,client,inactive',
        'notes'   => 'nullable|string|max:5000',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        // Check plan limit before allowing creation
        $sub = Subscription::where('company_id', auth()->user()->company_id)->with('plan')->first();
        if ($sub && $sub->plan->client_limit > 0 && Client::count() >= $sub->plan->client_limit) {
            session()->flash('error', "Limite de {$sub->plan->client_limit} clientes atingido. Faça upgrade do plano.");
            return;
        }

        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $client = Client::findOrFail($id);
        $this->editingId = $id;
        $this->name      = $client->name;
        $this->email     = $client->email ?? '';
        $this->phone     = $client->phone ?? '';
        $this->company   = $client->company ?? '';
        $this->status    = $client->status;
        $this->notes     = $client->notes ?? '';
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(CreateClientAction $createAction, UpdateClientAction $updateAction): void
    {
        $this->validate();

        $data = [
            'name'    => $this->name,
            'email'   => $this->email ?: null,
            'phone'   => $this->phone ?: null,
            'company' => $this->company ?: null,
            'status'  => $this->status,
            'notes'   => $this->notes ?: null,
        ];

        if ($this->isEditing) {
            $updateAction->execute(Client::findOrFail($this->editingId), $data);
            session()->flash('success', 'Cliente atualizado com sucesso!');
        } else {
            $createAction->execute($data);
            session()->flash('success', 'Cliente criado com sucesso!');
        }

        $this->closeModal();
    }

    public function delete(int $id): void
    {
        Client::findOrFail($id)->delete();
        session()->flash('success', 'Cliente removido.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->name = $this->email = $this->phone = $this->company = $this->notes = '';
        $this->status = 'lead';
        $this->editingId = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        $clients = Client::query()
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('company', 'like', "%{$this->search}%");
            }))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.clients.index', compact('clients'))
            ->layout('layouts.app', ['title' => 'Clientes']);
    }
}
