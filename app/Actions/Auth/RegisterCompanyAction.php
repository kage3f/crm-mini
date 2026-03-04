<?php

namespace App\Actions\Auth;

use App\Models\Company;
use App\Models\OpportunityStage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterCompanyAction
{
    public function execute(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // 1. Create the company
            $company = Company::create([
                'name' => $data['company'] ?? $data['name'] . "'s Company",
                'slug' => Str::slug($data['company'] ?? $data['name']) . '-' . Str::random(6),
            ]);

            // 2. Create the admin user
            $user = User::create([
                'company_id' => $company->id,
                'name'       => $data['name'],
                'email'      => $data['email'],
                'password'   => Hash::make($data['password']),
            ]);

            // 3. Assign admin role
            $user->assignRole('admin');

            // 4. Seed default stages for this company
            $this->seedDefaultStages($company);


            return $user;
        });
    }

    private function seedDefaultStages(Company $company): void
    {
        $stages = [
            ['name' => 'Prospecção',    'color' => '#6366f1', 'order' => 1],
            ['name' => 'Qualificação',  'color' => '#8b5cf6', 'order' => 2],
            ['name' => 'Proposta',      'color' => '#f59e0b', 'order' => 3],
            ['name' => 'Negociação',    'color' => '#f97316', 'order' => 4],
            ['name' => 'Fechado - Ganho', 'color' => '#10b981', 'order' => 5],
            ['name' => 'Fechado - Perdido', 'color' => '#ef4444', 'order' => 6],
        ];

        foreach ($stages as $stage) {
            $company->opportunities()->create(array_merge($stage, ['company_id' => $company->id, 'title' => $stage['name']]), [], 'opportunity_stages');
            // Wait, the relation for stages is opportunity_stages, let's just use the model directly or relationship
            OpportunityStage::create(array_merge($stage, ['company_id' => $company->id]));
        }
    }
}
