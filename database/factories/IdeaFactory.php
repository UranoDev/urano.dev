<?php

namespace Database\Factories;

use App\Enums\IdeaStatus;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Idea>
 */
class IdeaFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(6),
            'body' => fake()->paragraph(),
            'status' => IdeaStatus::Pending,
            'votes_count' => 0,
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => IdeaStatus::Pending]);
    }

    public function approved(): static
    {
        return $this->state(['status' => IdeaStatus::Approved]);
    }

    public function rejected(): static
    {
        return $this->state(['status' => IdeaStatus::Rejected]);
    }
}
