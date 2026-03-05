<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OpportunityStage>
 */
class OpportunityStageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->words(2, true),
            'color' => fake()->hexColor(),
            'order' => fake()->numberBetween(1, 10),
        ];
    }
}
