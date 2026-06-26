<?php

use App\Models\Service;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('services index page is accessible and shows all services', function () {
    $this->seed(ServiceSeeder::class);

    $response = $this->get(route('services.index'));

    $response->assertOk();
    $response->assertSee('SaaS de Reservas y Tours');
    $response->assertSee('Facturación CFDI 4.0');
    $response->assertSee('Procesamiento de Pagos');
});

test('services index shows links to each service detail page', function () {
    $this->seed(ServiceSeeder::class);

    $response = $this->get(route('services.index'));

    $response->assertOk();
    $response->assertSee(route('services.show', 'saas-reservas-tours'));
    $response->assertSee(route('services.show', 'facturacion-cfdi'));
});

test('a user can view any seeded service page successfully', function () {
    $this->seed(ServiceSeeder::class);

    $service = Service::where('slug', 'saas-reservas-tours')->first();

    $response = $this->get(route('services.show', ['slug' => 'saas-reservas-tours']));

    $response->assertOk();
    $response->assertSee($service->category);
    $response->assertSee($service->cta_text);
    $response->assertSee($service->benefits_title);
});

test('service detail page shows other services navigation', function () {
    $this->seed(ServiceSeeder::class);

    $response = $this->get(route('services.show', ['slug' => 'saas-reservas-tours']));

    $response->assertOk();
    $response->assertSee('Facturación CFDI 4.0');
    $response->assertSee('Procesamiento de Pagos');
});

test('viewing a non existent service returns 404', function () {
    $response = $this->get(route('services.show', ['slug' => 'non-existent-service-slug']));

    $response->assertNotFound();
});
