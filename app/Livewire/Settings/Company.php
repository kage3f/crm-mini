<?php

namespace App\Livewire\Settings;

use App\Models\Company as CompanyModel;
use Livewire\Component;

class Company extends Component
{
    public string $company_name = '';

    public function mount(): void
    {
        $company = auth()->user()->company;
        $this->company_name = $company?->name ?? '';
    }

    protected array $rules = [
        'company_name' => 'required|string|min:2|max:255',
    ];

    public function save(): void
    {
        $this->validate();
        $company = auth()->user()->company;
        $company?->update(['name' => $this->company_name]);
        session()->flash('success', 'Dados da empresa atualizados!');
    }

    public function render()
    {
        return view('livewire.settings.company')
            ->layout('layouts.app', ['title' => 'Empresa']);
    }
}
