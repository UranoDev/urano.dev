<?php

use App\Models\PortfolioProject;
use App\Services\PageSpeedInsightsService;
use Illuminate\Support\Facades\Http;

test('convierte los puntajes de Lighthouse de decimal a entero 0-100', function () {
    Http::fake([
        'googleapis.com/*' => Http::response([
            'lighthouseResult' => [
                'categories' => [
                    'performance' => ['score' => 1.0],
                    'accessibility' => ['score' => 0.98],
                    'best-practices' => ['score' => 0.75],
                    'seo' => ['score' => 0.5],
                ],
            ],
        ]),
    ]);

    $scores = (new PageSpeedInsightsService)->scoresFor('https://example.com');

    expect($scores)->toBe([
        'performance' => 100,
        'accessibility' => 98,
        'best_practices' => 75,
        'seo' => 50,
    ]);
});

test('regresa null si la petición falla', function () {
    Http::fake([
        'googleapis.com/*' => Http::response(['error' => 'quota exceeded'], 429),
    ]);

    expect((new PageSpeedInsightsService)->scoresFor('https://example.com'))->toBeNull();
});

test('el comando guarda los puntajes en el proyecto', function () {
    Http::fake([
        'googleapis.com/*' => Http::response([
            'lighthouseResult' => [
                'categories' => [
                    'performance' => ['score' => 0.9],
                    'accessibility' => ['score' => 0.9],
                    'best-practices' => ['score' => 0.9],
                    'seo' => ['score' => 0.9],
                ],
            ],
        ]),
    ]);

    $project = PortfolioProject::factory()->create(['url' => 'https://example.com']);

    $this->artisan('portfolio:refresh-pagespeed', ['slug' => $project->slug])
        ->assertExitCode(0);

    expect($project->fresh())
        ->pagespeed_performance->toBe(90)
        ->pagespeed_seo->toBe(90)
        ->pagespeed_measured_at->not->toBeNull();
});

test('el comando no falla si un proyecto no se puede medir', function () {
    Http::fake([
        'googleapis.com/*' => Http::response([], 500),
    ]);

    PortfolioProject::factory()->create(['url' => 'https://example.com']);

    $this->artisan('portfolio:refresh-pagespeed')
        ->assertExitCode(0);
});
