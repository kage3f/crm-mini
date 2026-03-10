<?php

namespace App\Http\Controllers;

use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class InvitationController extends Controller
{
    public function accept(string $token)
    {
        $invitation = TeamInvitation::where('token', $token)->firstOrFail();

        if (! $invitation->isValid()) {
            return redirect()->route('register')->with('error', 'Este convite expirou ou já foi utilizado.');
        }

        return view('auth.accept-invitation', compact('invitation'));
    }

    public function store(Request $request, string $token)
    {
        $invitation = TeamInvitation::where('token', $token)->firstOrFail();

        if (! $invitation->isValid()) {
            return redirect()->route('register')->with('error', 'Este convite expirou ou já foi utilizado.');
        }

        $request->validate([
            'name'                  => 'required|string|max:255',
            'password'              => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'company_id' => $invitation->company_id,
            'name'       => $request->name,
            'email'      => $invitation->email,
            'password'   => Hash::make($request->password),
        ]);

        $user->markEmailAsVerified();

        $user->assignRole($invitation->role);

        $invitation->update(['accepted_at' => now()]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Bem-vindo ao MiniCRM!');
    }
}
