<?php

namespace App\Livewire\Billing;

use App\Models\Plan;
use App\Models\Subscription;
use Livewire\Component;

class Plans extends Component
{
    public function render()
    {
        $plans = Plan::all();
        $currentPlan = Subscription::where('company_id', auth()->user()->company_id)
            ->with('plan')
            ->first()
            ?->plan;

        return view('livewire.billing.plans', compact('plans', 'currentPlan'))
            ->layout('layouts.app', ['title' => 'Planos e Preços']);
    }
}
