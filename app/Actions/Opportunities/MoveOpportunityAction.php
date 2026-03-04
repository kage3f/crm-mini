<?php

namespace App\Actions\Opportunities;

use App\Models\ActivityLog;
use App\Models\Opportunity;

class MoveOpportunityAction
{
    public function execute(Opportunity $opportunity, int $stageId): Opportunity
    {
        $old = $opportunity->stage?->name ?? 'N/A';
        $opportunity->update(['stage_id' => $stageId]);
        $opportunity->load('stage');
        ActivityLog::log(
            'opportunity_moved',
            "Oportunidade \"{$opportunity->title}\" movida de {$old} para {$opportunity->stage?->name}",
            $opportunity
        );
        return $opportunity;
    }
}
