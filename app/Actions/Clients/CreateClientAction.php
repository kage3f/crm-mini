<?php

namespace App\Actions\Clients;

use App\Models\ActivityLog;
use App\Models\Client;

class CreateClientAction
{
    public function execute(array $data): Client
    {
        $client = Client::create(array_merge($data, ['created_by' => auth()->id()]));
        return $client;
    }
}
