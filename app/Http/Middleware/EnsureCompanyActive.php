<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCompanyActive
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (! $user->email_verified_at) {
            return redirect()->route('verification.notice');
        }

        if (! $user->company_id) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Conta sem empresa associada.');
        }

        return $next($request);
    }
}
