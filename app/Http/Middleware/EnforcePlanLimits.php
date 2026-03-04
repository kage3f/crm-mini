<?php

namespace App\Http\Middleware;

use App\Models\Subscription;
use App\Models\Client;
use Closure;
use Illuminate\Http\Request;

class EnforcePlanLimits
{
    public function handle(Request $request, Closure $next, string $feature = 'clients')
    {
        $companyId = auth()->user()?->company_id;
        if (! $companyId) {
            return $next($request);
        }

        $subscription = Subscription::where('company_id', $companyId)
            ->with('plan')
            ->first();

        if (! $subscription || ! $subscription->plan) {
            return $next($request);
        }

        $plan = $subscription->plan;

        if ($feature === 'clients' && $plan->client_limit > 0) {
            $count = Client::count(); // HasCompany trait applies global scope
            if ($count >= $plan->client_limit) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Limite do plano atingido. Faça upgrade para continuar.'], 403);
                }
                return redirect()->route('billing.plans')
                    ->with('error', "Limite de {$plan->client_limit} clientes atingido no plano {$plan->name}. Faça upgrade para continuar.");
            }
        }

        return $next($request);
    }
}
