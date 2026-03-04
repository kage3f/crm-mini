<?php

namespace App\Livewire\Auth;

use App\Actions\Auth\RegisterCompanyAction;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Register extends Component
{
    public string $name     = '';
    public string $company  = '';
    public string $email    = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected array $rules = [
        'name'                  => 'required|string|min:2|max:255',
        'company'               => 'required|string|min:2|max:255',
        'email'                 => 'required|email|unique:users,email',
        'password'              => 'required|min:8|confirmed',
    ];


    public function register(RegisterCompanyAction $action)
    {
        $this->validate();

        $user = $action->execute([
            'name'     => $this->name,
            'company'  => $this->company,
            'email'    => $this->email,
            'password' => $this->password,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register')->layout('layouts.guest');
    }
}
