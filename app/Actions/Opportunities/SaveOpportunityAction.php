<?php

namespace App\Actions\Opportunities;

use App\Models\Opportunity;

class SaveOpportunityAction
{
    public function execute(array $data, ?Opportunity $opportunity = null): Opportunity
    {
        if ($opportunity) {
            $opportunity->update($data);
            return $opportunity->fresh();
        }

        return Opportunity::create(array_merge($data, [
            'created_by' => auth()->id(),
        ]));
    }
}
