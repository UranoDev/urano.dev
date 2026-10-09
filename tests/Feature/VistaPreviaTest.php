<?php

use App\Models\PortfolioProject;
use App\Models\PortfolioProjectScreenshot;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('la portada trae las etiquetas de vista previa con su título, descripción e imagen', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('<meta property="og:title" content="Urano Dev | Tecnología y Soluciones de Software para PYMEs y Turismo">', false);
    $response->assertSee('<meta property="og:description" content="Desarrollamos soluciones tecnológicas a la medida', false);
    $response->assertSee('<meta name="description" content="Desarrollamos soluciones tecnológicas a la medida', false);
    $response->assertSee('<meta property="og:image" content="'.asset('images/og/urano-dev.png').'">', false);
    $response->assertSee('<meta property="og:url" content="'.url('/').'">', false);
    $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
});

test('una página sin descripción propia usa la del sitio y nunca el título de la plantilla', function () {
    $response = $this->get(route('services.index'));

    $response->assertOk();
    $response->assertSee('<meta property="og:description" content="Software a la medida para PYMEs y empresas turísticas', false);
    $response->assertDontSee('Frost Laravel Theme');
});

test('el portafolio comparte su propia imagen', function () {
    $response = $this->get(route('portfolio.index'));

    $response->assertOk();
    $response->assertSee('<meta property="og:image" content="'.asset('images/og/portafolio.png').'">', false);
    $response->assertSee('<meta property="og:title" content="Portafolio | Urano Dev">', false);
});

test('un proyecto del portafolio comparte su lema y su captura destacada', function () {
    $project = PortfolioProject::factory()->create(['tagline' => 'Reseñas de Google desde el mostrador']);
    PortfolioProjectScreenshot::factory()->for($project)->create(['path' => 'images/portfolio/x/otra.png', 'is_featured' => false, 'sort_order' => 1]);
    PortfolioProjectScreenshot::factory()->for($project)->create(['path' => 'images/portfolio/x/hero.png', 'is_featured' => true, 'sort_order' => 2]);

    $response = $this->get(route('portfolio.show', $project));

    $response->assertOk();
    $response->assertSee('<meta property="og:description" content="Reseñas de Google desde el mostrador">', false);
    $response->assertSee('<meta property="og:image" content="'.asset('images/portfolio/x/hero.png').'">', false);
});

test('un caso de éxito comparte su resumen', function () {
    $response = $this->get(route('casos-exito.show', 'calzaclean'));

    $response->assertOk();
    $response->assertSee('<meta property="og:description" content="Un taller de una sola persona', false);
});

test('los enlaces del pie de página llevan a perfiles reales y tienen nombre', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertDontSee('href="#"', false);
    $response->assertSee('href="https://www.linkedin.com/in/uranogonzalez" aria-label="Urano González en LinkedIn"', false);
    $response->assertSee('href="https://www.youtube.com/@uranodev" aria-label="Urano Dev en YouTube"', false);
});

test('el sitio no carga fuentes de Google', function () {
    $this->get('/')->assertOk()->assertDontSee('fonts.googleapis.com', false);
});

test('los archivos compilados llevan hash en el nombre', function () {
    expect(file_get_contents(base_path('vite.config.js')))->not->toContain("entryFileNames: 'assets/[name].js'");
});
