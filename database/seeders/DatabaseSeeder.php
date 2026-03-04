<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\OpportunityStage;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles & Permissions (Global)
        $this->seedRolesAndPermissions();

        // 2. Plans (Global)
        $this->seedPlans();
    }

    private function seedRolesAndPermissions(): void
    {
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $member = Role::firstOrCreate(['name' => 'member']);

        $permissions = [
            'manage-team',
            'view-billing',
            'export-data',
        ];

        foreach ($permissions as $p) {
            $perm = Permission::firstOrCreate(['name' => $p]);
            $admin->givePermissionTo($perm);
        }
    }

    private function seedPlans(): void
    {
        Plan::firstOrCreate(['slug' => 'free'], [
            'name'          => 'Free',
            'price_monthly' => 0,
            'client_limit'  => 10,
            'user_limit'    => 3,
            'has_kanban'    => true,
            'has_tasks'     => true,
            'features'      => ['Até 10 clientes', 'Pipeline Kanban', 'Gestão de Tarefas'],
        ]);

        Plan::firstOrCreate(['slug' => 'pro'], [
            'name'          => 'Pro',
            'price_monthly' => 4900, // R$ 49,00
            'client_limit'  => -1,   // unlimited
            'user_limit'    => 10,
            'has_kanban'    => true,
            'has_tasks'     => true,
            'features'      => ['Clientes ilimitados', 'Até 10 membros', 'Suporte Prioritário'],
            'stripe_price_id' => 'price_pro_temp',
        ]);
    }
}
