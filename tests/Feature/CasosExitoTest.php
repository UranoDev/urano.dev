<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('el índice de casos de éxito lista cada caso con su enlace', function () {
    $response = $this->get(route('casos-exito.index'));

    $response->assertOk();
    $response->assertSee('CalzaClean');
    $response->assertSee(route('casos-exito.show', 'calzaclean'));
});

test('el caso de CalzaClean responde y cierra el enlace de la firma', function () {
    $response = $this->get('/casos-exito/calzaclean');

    $response->assertOk();
    $response->assertSee('CalzaClean');
});

test('el caso presenta la auditoría con su fecha', function () {
    $response = $this->get(route('casos-exito.show', 'calzaclean'));

    $response->assertSee('6 de septiembre de 2026');
    $response->assertSee('Seguidores en Instagram');
    $response->assertSee('Perfil de empresa en Google');
});

test('el caso lista las quince acciones con su estado', function () {
    $acciones = config('casos-exito.calzaclean.acciones');

    expect($acciones)->toHaveCount(15);

    $response = $this->get(route('casos-exito.show', 'calzaclean'));

    foreach ($acciones as $accion) {
        $response->assertSee($accion['accion'], escape: false);
    }
});

test('ninguna sección del caso menciona tecnologías', function () {
    $texto = file_get_contents(resource_path('views/casos-exito/calzaclean.blade.php'))
        .file_get_contents(resource_path('views/casos-exito/index.blade.php'))
        .file_get_contents(config_path('casos-exito.php'));

    $tecnologias = [
        'Laravel', 'Livewire', 'Blade', 'Tailwind', 'Alpine', 'Vite',
        'MySQL', 'SQLite', 'PHP', 'JavaScript', 'WordPress', 'Plesk',
    ];

    foreach ($tecnologias as $tecnologia) {
        expect($texto)->not->toContain($tecnologia);
    }
});

test('un caso que no existe devuelve un 404 propio del sitio', function () {
    $response = $this->get('/casos-exito/un-proyecto-que-no-existe');

    $response->assertNotFound();
    $response->assertSee('Esta dirección');
    $response->assertSee(route('casos-exito.index'));
});
