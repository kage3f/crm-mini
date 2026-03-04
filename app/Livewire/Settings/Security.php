<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Security extends Component
{
    public string $current_password      = '';
    public string $new_password          = '';
    public string $new_password_confirmation = '';

    protected function rules(): array
    {
        return [
            'current_password'      => 'required',
            'new_password'          => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function changePassword(): void
    {
        $this->validate();

        if (! Hash::check($this->current_password, auth()->user()->password)) {
            $this->addError('current_password', 'Senha atual incorreta.');
            return;
        }

        auth()->user()->update(['password' => Hash::make($this->new_password)]);

        $this->current_password = $this->new_password = $this->new_password_confirmation = '';
        session()->flash('success', 'Senha alterada com sucesso!');
    }

    public function render()
    {
        return view('livewire.settings.security')
            ->layout('layouts.app', ['title' => 'Segurança']);
    }
}
