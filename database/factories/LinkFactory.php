<?php

namespace Database\Factories;

use App\Enums\LinkType;
use App\Models\Link;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Link>
 */
class LinkFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'url' => fake()->url(),
            'type' => LinkType::External,
            'post_id' => null,
            'sort_order' => fake()->numberBetween(0, 100),
            'is_active' => true,
        ];
    }

    public function external(): static
    {
        return $this->state([
            'type' => LinkType::External,
            'post_id' => null,
            'url' => fake()->url(),
        ]);
    }

    public function internal(): static
    {
        return $this->state([
            'type' => LinkType::Internal,
            'url' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
