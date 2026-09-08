<?php

use App\Models\Post;
use App\Models\User;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('el sitemap se sirve como xml', function () {
    $response = $this->get('/sitemap.xml');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/xml');
    $response->assertSee('<urlset', escape: false);
});

test('el sitemap incluye el índice de casos y cada caso publicado', function () {
    $response = $this->get(route('sitemap'));

    $response->assertOk();
    $response->assertSee(route('casos-exito.index'), escape: false);

    foreach (array_keys(config('casos-exito')) as $proyecto) {
        $response->assertSee(route('casos-exito.show', $proyecto), escape: false);
    }
});

test('el sitemap incluye las páginas fijas del sitio', function () {
    $response = $this->get(route('sitemap'));

    $response->assertSee(route('home'), escape: false);
    $response->assertSee(route('services.index'), escape: false);
    $response->assertSee(route('blog.index'), escape: false);
    $response->assertSee(route('nosotros'), escape: false);
    $response->assertSee(route('contact'), escape: false);
});

test('el sitemap incluye cada servicio', function () {
    $this->seed(ServiceSeeder::class);

    $response = $this->get(route('sitemap'));

    $response->assertSee(route('services.show', 'saas-reservas-tours'), escape: false);
});

test('el sitemap solo incluye posts publicados', function () {
    $autor = User::factory()->author()->create();

    Post::factory()->for($autor, 'author')->create([
        'slug' => 'un-post-publicado',
        'status' => 'published',
    ]);

    Post::factory()->for($autor, 'author')->create([
        'slug' => 'un-borrador',
        'status' => 'draft',
    ]);

    $response = $this->get(route('sitemap'));

    $response->assertSee(route('blog.show', 'un-post-publicado'), escape: false);
    $response->assertDontSee(route('blog.show', 'un-borrador'), escape: false);
});

test('robots.txt apunta al sitemap', function () {
    expect(file_get_contents(public_path('robots.txt')))
        ->toContain('Sitemap: https://urano.dev/sitemap.xml');
});
