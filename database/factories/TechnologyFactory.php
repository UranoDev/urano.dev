<?php

namespace Database\Factories;

use App\Enums\TechnologyCategory;
use App\Enums\TechnologyGroup;
use App\Models\Technology;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Technology>
 */
class TechnologyFactory extends Factory
{
    protected $model = Technology::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'slug' => str($name)->slug(),
            'name' => ucfirst($name),
            'group' => TechnologyGroup::Stack,
            'category' => fake()->randomElement(TechnologyCategory::cases()),
            'color' => fake()->hexColor(),
        ];
    }
}
