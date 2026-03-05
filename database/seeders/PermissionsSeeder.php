<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Seed roles and permissions (global).
     */
    public function run(): void
    {
        $guard = config('auth.defaults.guard', 'web');

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => $guard,
        ]);

        Role::firstOrCreate([
            'name' => 'member',
            'guard_name' => $guard,
        ]);

        $permissions = [
            'manage-team',
            'manage-permissions',
            'view-billing',
            'export-data',
            'clients.view',
            'clients.create',
            'clients.update',
            'clients.delete',
            'opportunities.view',
            'opportunities.create',
            'opportunities.update',
            'opportunities.delete',
            'tasks.view',
            'tasks.create',
            'tasks.update',
            'tasks.delete',
            'tasks.assign',
        ];

        foreach ($permissions as $p) {
            $perm = Permission::firstOrCreate([
                'name' => $p,
                'guard_name' => $guard,
            ]);
            $admin->givePermissionTo($perm);
        }
    }
}
