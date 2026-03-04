<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;

class Show extends Component
{
    public Client $client;

    public function mount(Client $client)
    {
        $this->client = $client->load(['opportunities.stage', 'tasks']);
    }

    public function render()
    {
        return view('livewire.clients.show', [
            'client' => $this->client,
        ])->layout('layouts.app', ['title' => $this->client->name]);
    }
}
