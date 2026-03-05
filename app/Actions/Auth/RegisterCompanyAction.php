<?php

namespace App\Actions\Auth;

use App\Models\Company;
use App\Models\User;
use Database\Seeders\CompanyDefaultStagesSeeder;
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
            app(CompanyDefaultStagesSeeder::class)->seedForCompany($company);

            return $user;
        });
    }
}
