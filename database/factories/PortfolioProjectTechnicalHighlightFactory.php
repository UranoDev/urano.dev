<?php

namespace Database\Factories;

use App\Models\PortfolioProject;
use App\Models\PortfolioProjectTechnicalHighlight;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PortfolioProjectTechnicalHighlight>
 */
class PortfolioProjectTechnicalHighlightFactory extends Factory
{
    protected $model = PortfolioProjectTechnicalHighlight::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'portfolio_project_id' => PortfolioProject::factory(),
            'description' => fake()->sentence(12),
            'technology_ids' => null,
            'sort_order' => 0,
        ];
    }
}
