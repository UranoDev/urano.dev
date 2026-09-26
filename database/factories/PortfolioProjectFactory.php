<?php

namespace Database\Factories;

use App\Enums\PortfolioProjectCategory;
use App\Enums\PortfolioProjectStatus;
use App\Models\PortfolioProject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PortfolioProject>
 */
class PortfolioProjectFactory extends Factory
{
    protected $model = PortfolioProject::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(2, true);

        return [
            'slug' => str($title)->slug(),
            'title' => ucwords($title),
            'tagline' => fake()->sentence(8),
            'url' => fake()->optional()->domainName(),
            'status' => PortfolioProjectStatus::Live,
            'category' => fake()->randomElement(PortfolioProjectCategory::cases()),
            'started_year' => fake()->numberBetween(2023, 2026),
            'started_month' => fake()->numberBetween(1, 12),
            'ended_year' => null,
            'ended_month' => null,
            'description' => fake()->paragraph(),
            'features' => fake()->sentences(4),
            'sort_order' => 0,
        ];
    }
}
