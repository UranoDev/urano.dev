<?php

use App\Models\User;

test('los visitantes verificados son redirigidos al home al intentar acceder al dashboard', function () {
    $visitor = User::factory()->visitor()->create();

    $this->actingAs($visitor)
        ->get(route('dashboard'))
        ->assertRedirect(route('home'));
});

test('los visitantes no verificados son redirigidos a verificar email', function () {
    $visitor = User::factory()->visitor()->unverified()->create();

    $this->actingAs($visitor)
        ->get(route('dashboard'))
        ->assertRedirect(route('verification.notice'));
});

test('los autores pueden acceder al dashboard', function () {
    $author = User::factory()->author()->create();

    $this->actingAs($author)
        ->get(route('dashboard'))
        ->assertOk();
});

test('los administradores pueden acceder al dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk();
});

test('el autor ve la seccion Contenido pero no la seccion Administracion', function () {
    $author = User::factory()->author()->create();

    $response = $this->actingAs($author)->get(route('dashboard'));

    $response->assertSee('Contenido')
        ->assertSee('Posts')
        ->assertSee(route('posts.index'), false)
        ->assertDontSee('Administraci');
});

test('el administrador ve la seccion Contenido y la seccion Administracion', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertSee('Contenido')
        ->assertSee('Posts')
        ->assertSee(route('posts.index'), false)
        ->assertSee('Administraci')
        ->assertSee('Ideas')
        ->assertSee('Links')
        ->assertSee('Usuarios')
        ->assertSee(route('users.index'), false);
});

test('un autor inactivo es redirigido al home al intentar acceder al dashboard', function () {
    $author = User::factory()->author()->inactive()->create();

    $this->actingAs($author)
        ->get(route('dashboard'))
        ->assertRedirect(route('home'));
});

test('un administrador inactivo es redirigido al home al intentar acceder al dashboard', function () {
    $admin = User::factory()->admin()->inactive()->create();

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertRedirect(route('home'));});

test('el dashboard muestra el link de Dashboard para todos los roles con acceso', function () {
    foreach ([User::factory()->author()->create(), User::factory()->admin()->create()] as $user) {
        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertSee('Dashboard');
    }
});
