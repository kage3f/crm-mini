<?php

namespace Database\Factories;

use App\Models\OpportunityStage;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Opportunity>
 */
class OpportunityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'title' => fake()->sentence(3),
            'client_id' => null,
            'stage_id' => function (array $attributes) {
                return OpportunityStage::factory()
                    ->create(['company_id' => $attributes['company_id']])
                    ->id;
            },
            'value' => fake()->randomFloat(2, 100, 10000),
            'expected_close_date' => fake()->date(),
            'notes' => fake()->sentence(),
            'assigned_to' => null,
            'created_by' => null,
        ];
    }
}
