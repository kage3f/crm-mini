<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    /**
     * Simulate a Stripe payment (no real Stripe integration for now).
     */
    public function simulate(Request $request, Plan $plan)
    {
        $companyId = auth()->user()->company_id;

        $subscription = Subscription::where('company_id', $companyId)->first();

        if ($subscription) {
            $subscription->update([
                'plan_id' => $plan->id,
                'status'  => 'active',
                'stripe_subscription_id' => 'sim_' . strtoupper(str_replace('-', '', (string)$companyId)),
            ]);
        } else {
            Subscription::create([
                'company_id' => $companyId,
                'plan_id'    => $plan->id,
                'status'     => 'active',
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', "Plano {$plan->name} ativado com sucesso! (Pagamento simulado)");
    }
}
