<?php

namespace Database\Factories;

use App\Models\Link;
use App\Models\LinkClick;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LinkClick>
 */
class LinkClickFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'link_id' => Link::factory(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }
}
