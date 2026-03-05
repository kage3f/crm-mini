<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\OpportunityStage;
use Illuminate\Database\Seeder;

class CompanyDefaultStagesSeeder extends Seeder
{
    /**
     * Seed default stages for all existing companies.
     */
    public function run(): void
    {
        Company::query()->each(function (Company $company): void {
            $this->seedForCompany($company);
        });
    }

    public function seedForCompany(Company $company): void
    {
        foreach ($this->defaultStages() as $stage) {
            OpportunityStage::query()->firstOrCreate(
                [
                    'company_id' => $company->id,
                    'name' => $stage['name'],
                ],
                [
                    'color' => $stage['color'],
                    'order' => $stage['order'],
                ]
            );
        }
    }

    private function defaultStages(): array
    {
        return [
            ['name' => 'Prospecção', 'color' => '#6366f1', 'order' => 1],
            ['name' => 'Qualificação', 'color' => '#8b5cf6', 'order' => 2],
            ['name' => 'Proposta', 'color' => '#f59e0b', 'order' => 3],
            ['name' => 'Negociação', 'color' => '#f97316', 'order' => 4],
            ['name' => 'Fechado - Ganho', 'color' => '#10b981', 'order' => 5],
            ['name' => 'Fechado - Perdido', 'color' => '#ef4444', 'order' => 6],
        ];
    }
}
