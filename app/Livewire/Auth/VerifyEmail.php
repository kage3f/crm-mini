<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class VerifyEmail extends Component
{
    public function resendEmail()
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        Auth::user()->sendEmailVerificationNotification();

        session()->flash('success', 'Email de verificação reenviado!');

        return true;
    }

    public function render()
    {
        return view('livewire.auth.verify-email')->layout('layouts.guest');
    }
}
