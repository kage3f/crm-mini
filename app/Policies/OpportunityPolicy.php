<?php

namespace App\Policies;

use App\Models\Opportunity;
use App\Models\User;

class OpportunityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasCompany();
    }

    public function create(User $user): bool
    {
        return $user->hasCompany();
    }

    public function update(User $user, Opportunity $opportunity): bool
    {
        return $user->company_id === $opportunity->company_id;
    }

    public function delete(User $user, Opportunity $opportunity): bool
    {
        return $user->company_id === $opportunity->company_id;
    }
}
