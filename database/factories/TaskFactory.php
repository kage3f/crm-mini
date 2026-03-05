<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Opportunity;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'in_progress', 'done']);

        $dueDate = $this->faker->optional()->dateTimeBetween('-5 days', '+10 days');

        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional()->paragraph(),
            'client_id' => null,
            'opportunity_id' => null,
            'assigned_to' => null,
            'created_by' => null,
            'status' => $status,
            'due_date' => $dueDate ? $dueDate->format('Y-m-d') : null,
        ];
    }

    public function forClient(Client $client): self
    {
        return $this->state(fn () => ['client_id' => $client->id]);
    }

    public function forOpportunity(Opportunity $opportunity): self
    {
        return $this->state(fn () => ['opportunity_id' => $opportunity->id]);
    }

    public function assignedTo(User $user): self
    {
        return $this->state(fn () => ['assigned_to' => $user->id]);
    }

    public function createdBy(User $user): self
    {
        return $this->state(fn () => ['created_by' => $user->id]);
    }
}
