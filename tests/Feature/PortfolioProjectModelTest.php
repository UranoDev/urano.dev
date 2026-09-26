<?php

use App\Enums\PortfolioProjectCategory;
use App\Enums\PortfolioProjectStatus;
use App\Enums\TechnologyCategory;
use App\Models\PortfolioProject;
use App\Models\PortfolioProjectScreenshot;
use App\Models\PortfolioProjectTechnicalHighlight;
use App\Models\Technology;

test('un proyecto tiene estado y categoría por defecto', function () {
    $project = PortfolioProject::factory()->create();

    expect($project->status)->toBe(PortfolioProjectStatus::Live)
        ->and($project->category)->toBeInstanceOf(PortfolioProjectCategory::class)
        ->and($project->features)->toBeArray();
});

test('un proyecto tiene tecnologías ordenadas por el pivote', function () {
    $project = PortfolioProject::factory()->create();
    $laravel = Technology::factory()->create(['slug' => 'laravel', 'category' => TechnologyCategory::Backend]);
    $tailwind = Technology::factory()->create(['slug' => 'tailwind', 'category' => TechnologyCategory::Frontend]);

    $project->technologies()->attach([
        $tailwind->id => ['sort_order' => 1],
        $laravel->id => ['sort_order' => 0],
    ]);

    expect($project->technologies()->pluck('slug')->all())->toBe(['laravel', 'tailwind']);
});

test('un proyecto tiene capturas ordenadas y una destacada', function () {
    $project = PortfolioProject::factory()->create();
    PortfolioProjectScreenshot::factory()->for($project, 'portfolioProject')->create(['sort_order' => 1]);
    $hero = PortfolioProjectScreenshot::factory()->for($project, 'portfolioProject')->featured()->create(['sort_order' => 0]);

    expect($project->screenshots)->toHaveCount(2)
        ->and($project->screenshots->first()->is($hero))->toBeTrue()
        ->and($project->featuredScreenshot()->is($hero))->toBeTrue();
});

test('una funcionalidad técnica puede referenciar tecnologías por id', function () {
    $project = PortfolioProject::factory()->create();
    $deepseek = Technology::factory()->create(['slug' => 'deepseek', 'category' => TechnologyCategory::Ai]);

    $highlight = PortfolioProjectTechnicalHighlight::factory()
        ->for($project, 'portfolioProject')
        ->create(['technology_ids' => [$deepseek->id]]);

    expect($highlight->relatedTechnologies()->pluck('slug')->all())->toBe(['deepseek']);
});

test('borrar un proyecto borra en cascada sus capturas, tecnologías y funcionalidades', function () {
    $project = PortfolioProject::factory()->create();
    $technology = Technology::factory()->create();
    $project->technologies()->attach($technology);
    PortfolioProjectScreenshot::factory()->for($project, 'portfolioProject')->create();
    PortfolioProjectTechnicalHighlight::factory()->for($project, 'portfolioProject')->create();

    $project->delete();

    expect(PortfolioProjectScreenshot::count())->toBe(0)
        ->and(PortfolioProjectTechnicalHighlight::count())->toBe(0)
        ->and($project->technologies()->count())->toBe(0)
        ->and(Technology::count())->toBe(1);
});
