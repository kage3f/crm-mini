<?php

namespace App\Actions\Opportunities;

use App\Models\ActivityLog;
use App\Models\Opportunity;

class DeleteOpportunityAction
{
    public function execute(Opportunity $opportunity): void
    {
        ActivityLog::log(
            'opportunity_deleted',
            "Oportunidade \"{$opportunity->title}\" deletada",
            $opportunity
        );
        $opportunity->delete();
    }
}
