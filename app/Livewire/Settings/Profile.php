<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Profile extends Component
{
    public string $name  = '';
    public string $email = '';

    public function mount(): void
    {
        $this->name  = auth()->user()->name;
        $this->email = auth()->user()->email;
    }

    protected array $rules = [
        'name'  => 'required|string|min:2|max:255',
        'email' => 'required|email|unique:users,email,' . 0, // will be overridden
    ];

    protected function rules(): array
    {
        return [
            'name'  => 'required|string|min:2|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ];
    }

    public function save(): void
    {
        $this->validate();
        auth()->user()->update(['name' => $this->name, 'email' => $this->email]);
        session()->flash('success', 'Perfil atualizado com sucesso!');
    }

    public function render()
    {
        return view('livewire.settings.profile')
            ->layout('layouts.app', ['title' => 'Perfil']);
    }
}
