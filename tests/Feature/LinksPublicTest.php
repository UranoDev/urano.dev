<?php

use App\Models\Link;
use App\Models\User;
test('la pagina publica de links es accesible sin autenticacion', function () {
    $this->get(route('links.public'))
        ->assertOk();
});

test('la pagina publica muestra los links activos', function () {
    Link::factory()->create(['title' => 'Mi GitHub', 'is_active' => true]);
    Link::factory()->create(['title' => 'Mi Twitter', 'is_active' => true]);

    $this->get(route('links.public'))
        ->assertSeeText('Mi GitHub')
        ->assertSeeText('Mi Twitter');
});

test('la pagina publica no muestra links inactivos', function () {
    Link::factory()->create(['title' => 'Link visible', 'is_active' => true]);
    Link::factory()->inactive()->create(['title' => 'Link oculto']);

    $this->get(route('links.public'))
        ->assertSeeText('Link visible')
        ->assertDontSeeText('Link oculto');
});

test('los links de la pagina publica apuntan a la ruta de tracking', function () {
    $link = Link::factory()->create(['is_active' => true]);

    $this->get(route('links.public'))
        ->assertSee(route('links.click', $link));
});

test('la pagina publica muestra los links ordenados por sort_order', function () {
    Link::factory()->create(['title' => 'Tercero', 'sort_order' => 2, 'is_active' => true]);
    Link::factory()->create(['title' => 'Primero', 'sort_order' => 0, 'is_active' => true]);
    Link::factory()->create(['title' => 'Segundo', 'sort_order' => 1, 'is_active' => true]);

    $this->get(route('links.public'))
        ->assertSeeInOrder(['Primero', 'Segundo', 'Tercero']);
});

test('la pagina publica muestra mensaje cuando no hay links', function () {
    $this->get(route('links.public'))
        ->assertSeeText('No hay links disponibles por ahora.');
});

test('muestra el circulo por defecto cuando no hay links o el primer link no tiene owner', function () {
    $this->get(route('links.public'))
        ->assertSee('data-test="owner-avatar-placeholder"', false)
        ->assertDontSee('data-test="owner-avatar"', false);

    Link::factory()->create(['is_active' => true, 'user_id' => null]);

    $this->get(route('links.public'))
        ->assertSee('data-test="owner-avatar-placeholder"', false)
        ->assertDontSee('data-test="owner-avatar"', false);
});

test('muestra las iniciales del owner cuando este no tiene avatar', function () {
    $owner = User::factory()->create([
        'name' => 'Carla Diaz',
        'avatar' => null,
    ]);

    Link::factory()->create([
        'is_active' => true,
        'user_id' => $owner->id,
        'sort_order' => 0,
    ]);

    $this->get(route('links.public'))
        ->assertSee('data-test="owner-avatar-placeholder"', false)
        ->assertSeeText('CD')
        ->assertDontSee('data-test="owner-avatar"', false);
});

test('muestra la foto/avatar del owner del primer link si existe', function () {
    $owner = User::factory()->create([
        'name' => 'Esteban Quito',
        'avatar' => 'avatars/esteban.png',
    ]);

    Link::factory()->create([
        'is_active' => true,
        'user_id' => $owner->id,
        'sort_order' => 0,
    ]);

    $this->get(route('links.public'))
        ->assertSee('data-test="owner-avatar"', false)
        ->assertSee('storage/avatars/esteban.png', false)
        ->assertDontSee('data-test="owner-avatar-placeholder"', false);
});

test('los links de la pagina publica se abren en una nueva pestaña', function () {
    $link = Link::factory()->create(['is_active' => true]);

    $this->get(route('links.public'))
        ->assertSee('target="_blank"', false)
        ->assertSee('rel="noopener noreferrer"', false);
});