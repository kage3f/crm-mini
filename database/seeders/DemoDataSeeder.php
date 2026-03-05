<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Company;
use App\Models\Opportunity;
use App\Models\OpportunityStage;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::query()->firstOrCreate(
            ['name' => 'DevSquad'],
            ['slug' => Str::slug('DevSquad') . '-' . Str::lower(Str::random(6))]
        );

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin DevSquad',
                'password' => Hash::make('senha'),
                'company_id' => $company->id,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Ensure default stages exist for this company
        (new CompanyDefaultStagesSeeder())->seedForCompany($company);

        $stages = OpportunityStage::query()
            ->where('company_id', $company->id)
            ->orderBy('order')
            ->get();

        // Team members
        $members = User::factory()
            ->count(6)
            ->create(['company_id' => $company->id])
            ->each(function (User $user): void {
                $user->assignRole('member');
                $user->givePermissionTo([
                    'clients.view',
                    'opportunities.view',
                    'tasks.view',
                ]);
            });

        $users = $members->push($admin);

        // Clients
        $clients = Client::factory()
            ->count(25)
            ->create([
                'company_id' => $company->id,
            ])
            ->each(function (Client $client) use ($users): void {
                $client->update(['created_by' => $users->random()->id]);
            });

        // Opportunities (distribuir entre estágios)
        $distribution = [
            'Prospecção' => 12,
            'Qualificação' => 10,
            'Proposta' => 8,
            'Negociação' => 6,
            'Fechado - Ganho' => 4,
            'Fechado - Perdido' => 4,
        ];

        $opportunities = collect();

        foreach ($stages as $stage) {
            $count = $distribution[$stage->name] ?? 4;

            $created = Opportunity::factory()
                ->count($count)
                ->make([
                    'company_id' => $company->id,
                    'stage_id' => $stage->id,
                ])
                ->each(function (Opportunity $opp) use ($clients, $users): void {
                    $opp->client_id = $clients->random()->id;
                    $opp->created_by = $users->random()->id;
                    $opp->assigned_to = $users->random()->id;
                    $opp->expected_close_date = now()->addDays(rand(-10, 20))->format('Y-m-d');
                    $opp->save();
                });

            $opportunities = $opportunities->merge($created);
        }

        // Tasks
        Task::factory()
            ->count(60)
            ->create([
                'company_id' => $company->id,
            ])
            ->each(function (Task $task) use ($clients, $opportunities, $users): void {
                $task->update([
                    'client_id' => $clients->random()->id,
                    'opportunity_id' => $opportunities->random()->id,
                    'created_by' => $users->random()->id,
                    'assigned_to' => $users->random()->id,
                ]);
            });
    }
}
