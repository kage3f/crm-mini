<?php

namespace App\Livewire\Billing;

use App\Models\Plan;
use Livewire\Component;

class Checkout extends Component
{
    public Plan $plan;

    public string $cardName   = '';
    public string $cardNumber = '';
    public string $cardExpiry = '';
    public string $cardCvv    = '';

    protected array $rules = [
        'cardName'   => 'required|string',
        'cardNumber' => 'required|digits:16',
        'cardExpiry' => 'required|string',
        'cardCvv'    => 'required|digits_between:3,4',
    ];

    public function mount(Plan $plan)
    {
        $this->plan = $plan;
    }

    public function render()
    {
        return view('livewire.billing.checkout', [
            'plan' => $this->plan,
        ])->layout('layouts.app', ['title' => 'Checkout — ' . $this->plan->name]);
    }
}
