<?php

namespace Database\Factories;

use App\Models\PortfolioProject;
use App\Models\PortfolioProjectScreenshot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PortfolioProjectScreenshot>
 */
class PortfolioProjectScreenshotFactory extends Factory
{
    protected $model = PortfolioProjectScreenshot::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'portfolio_project_id' => PortfolioProject::factory(),
            'path' => 'images/portfolio/'.fake()->slug(2).'.png',
            'alt' => fake()->sentence(4),
            'is_featured' => false,
            'sort_order' => 0,
        ];
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }
}
