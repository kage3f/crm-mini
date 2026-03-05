<?php

namespace Database\Factories;

use App\Models\TeamInvitation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TeamInvitationFactory extends Factory
{
    protected $model = TeamInvitation::class;

    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Company::factory(),
            'email' => $this->faker->unique()->safeEmail(),
            'token' => Str::random(64),
            'role' => $this->faker->randomElement(['admin', 'member']),
            'accepted_at' => null,
            'expires_at' => now()->addDays(7),
        ];
    }
}
