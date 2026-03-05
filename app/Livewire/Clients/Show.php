<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;

class Show extends Component
{
    public Client $client;

    public function mount(Client $client)
    {
        $this->client = $client;
    }

    public function render()
    {
        // Reload the client with relationships to ensure fresh data
        $client = $this->client->load(['opportunities.stage', 'tasks']);

        return view('livewire.clients.show', [
            'client' => $client,
        ])->layout('layouts.app', ['title' => $client->name]);
    }
}
