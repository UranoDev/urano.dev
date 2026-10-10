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
    $response->assertSee('<meta property="og:image" content="'.asset('images/og/urano-dev-2x.png').'">', false);
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
    $response->assertSee('<meta property="og:image" content="'.asset('images/og/portafolio-2x.png').'">', false);
    $response->assertSee('<meta property="og:title" content="Portafolio | Urano Dev">', false);
});

test('un proyecto del portafolio comparte su descripción y su captura destacada', function () {
    $project = PortfolioProject::factory()->create([
        'tagline' => 'Reseñas de Google desde el mostrador',
        'description' => 'Un dispositivo que un negocio deja en el mostrador para que el cliente deje su reseña en Google.',
    ]);
    PortfolioProjectScreenshot::factory()->for($project)->create(['path' => 'images/portfolio/x/otra.png', 'is_featured' => false, 'sort_order' => 1]);
    PortfolioProjectScreenshot::factory()->for($project)->create(['path' => 'images/portfolio/x/hero.png', 'is_featured' => true, 'sort_order' => 2]);

    $response = $this->get(route('portfolio.show', $project));

    $response->assertOk();
    $response->assertSee('<meta property="og:description" content="Un dispositivo que un negocio deja en el mostrador para que el cliente deje su reseña en Google.">', false);
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

test('los encabezados de las páginas públicas no saltan niveles', function () {
    $project = PortfolioProject::factory()->create();

    foreach (['/', '/servicios', '/portafolio', route('portfolio.show', $project), '/nosotros', '/contacto', '/blog'] as $pagina) {
        $html = $this->get($pagina)->assertOk()->getContent();

        preg_match_all('/<h([1-6])\b/', $html, $niveles);

        expect($niveles[1])->toContain('1');

        $anterior = 0;
        foreach (array_map('intval', $niveles[1]) as $nivel) {
            expect($nivel)->toBeLessThanOrEqual($anterior + 1, "$pagina salta de h$anterior a h$nivel");
            $anterior = $nivel;
        }
    }
});

test('las descripciones para compartir tienen al menos 100 caracteres', function () {
    $project = PortfolioProject::factory()->create([
        'tagline' => 'Lema corto del proyecto',
        'description' => str_repeat('Descripción larga del proyecto con suficiente detalle. ', 8),
    ]);

    foreach (['/', '/portafolio', route('portfolio.show', $project), '/servicios', '/nosotros', '/contacto', '/blog'] as $pagina) {
        $html = $this->get($pagina)->assertOk()->getContent();

        preg_match('/<meta property="og:description" content="([^"]*)">/', $html, $m);

        expect(mb_strlen(html_entity_decode($m[1] ?? '')))->toBeGreaterThanOrEqual(100, "$pagina tiene una descripción corta");
    }
});

test('el sitio declara a su autor', function () {
    $this->get('/')->assertOk()->assertSee('<meta name="author" content="Urano Gonzalez">', false);
});
