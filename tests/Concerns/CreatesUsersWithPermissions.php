<?php

namespace Tests\Concerns;

use App\Models\User;

trait CreatesUsersWithPermissions
{
    protected function createUserWithPermissions(array $permissions = []): User
    {
        $user = User::factory()->create();
        $user->assignRole('member');

        if (! empty($permissions)) {
            $user->givePermissionTo($permissions);
        }

        return $user;
    }
}
