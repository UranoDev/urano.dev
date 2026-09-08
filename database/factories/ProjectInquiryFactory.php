<?php

namespace Database\Factories;

use App\Enums\ProjectTimeframe;
use App\Models\ProjectInquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectInquiry>
 */
class ProjectInquiryFactory extends Factory
{
    protected $model = ProjectInquiry::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'company' => fake()->company(),
            'project_description' => fake()->paragraph(),
            'timeframe' => fake()->randomElement(ProjectTimeframe::cases()),
        ];
    }
}
