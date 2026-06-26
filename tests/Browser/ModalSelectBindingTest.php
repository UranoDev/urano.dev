<?php

use App\Enums\LinkType;
use App\Enums\Role;
use App\Models\Link;
use App\Models\Post;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Regresión: binding de flux:select dentro de flux:modal
|--------------------------------------------------------------------------
|
| Estos tests reproducen en un navegador real el bug que los tests de
| Livewire (Livewire::test()->set()) no pueden detectar: los selects dentro
| de un flux:modal teletransportado no persistían su valor con wire:model
| diferido. Solo un test de navegador ejercita el binding real del DOM.
|
*/

test('cambiar el rol en el modal de usuarios persiste en la BD', function () {
    $admin = User::factory()->admin()->create();
    $author = User::factory()->author()->create(['name' => 'Autor Objetivo']);

    $this->actingAs($admin);

    $page = visit('/dashboard/users');

    $page->click('[wire\\:click="openEdit('.$author->id.')"]')
        ->assertSee('Editar usuario')
        ->select('@role-select', 'admin')
        ->click('Guardar cambios')
        ->assertSee('Usuario actualizado.')
        ->assertNoJavascriptErrors();

    expect($author->fresh()->role)->toBe(Role::Admin);
});

test('cambiar el propietario en el modal de links persiste en la BD', function () {
    $admin = User::factory()->admin()->create(['name' => 'Admin Logueado']);
    $otherAdmin = User::factory()->admin()->create(['name' => 'Admin Propietario']);

    $link = Link::factory()->external()->create([
        'title' => 'Link de prueba',
        'user_id' => $admin->id,
    ]);

    $this->actingAs($admin);

    $page = visit('/dashboard/links');

    $page->click('[wire\\:click="openEdit('.$link->id.')"]')
        ->assertSee('Editar link')
        ->select('@owner-select', (string) $otherAdmin->id)
        ->click('Guardar cambios')
        ->assertSee('Link actualizado.')
        ->assertNoJavascriptErrors();

    expect($link->fresh()->user_id)->toBe($otherAdmin->id);
});

test('crear un link interno guarda el post asociado y el propietario', function () {
    $admin = User::factory()->admin()->create(['name' => 'Admin Logueado']);
    $owner = User::factory()->admin()->create(['name' => 'Admin Duenio']);
    $post = Post::factory()->create([
        'title' => 'Post Publicado',
        'status' => 'published',
    ]);

    $this->actingAs($admin);

    $page = visit('/dashboard/links');

    $page->click('Nuevo link')
        ->assertSee('Nuevo link')
        ->type('@link-title', 'Link interno de prueba')
        ->select('@type-select', 'internal')
        ->select('@post-select', (string) $post->id)
        ->select('@owner-select', (string) $owner->id)
        ->click('Crear link')
        ->assertSee('Link creado.')
        ->assertNoJavascriptErrors();

    $link = Link::where('title', 'Link interno de prueba')->first();

    expect($link)->not->toBeNull()
        ->and($link->type)->toBe(LinkType::Internal)
        ->and($link->post_id)->toBe($post->id)
        ->and($link->user_id)->toBe($owner->id);
});

test('editar un link sin owner con un solo admin guarda ese admin al dar guardar', function () {
    // Caso real del bug: el select muestra una sola opción (un admin) que NO
    // coincide con el valor en BD (null). El navegador la auto-muestra pero no
    // dispara change; al guardar sin tocar el select, debe persistir ese admin.
    $admin = User::factory()->admin()->create(['name' => 'Unico Admin']);

    $link = Link::factory()->external()->create([
        'title' => 'Link sin owner',
        'user_id' => null,
    ]);

    $this->actingAs($admin);

    $page = visit('/dashboard/links');

    $page->click('[wire\\:click="openEdit('.$link->id.')"]')
        ->assertSee('Editar link')
        ->click('Guardar cambios')
        ->assertSee('Link actualizado.')
        ->assertNoJavascriptErrors();

    expect($link->fresh()->user_id)->toBe($admin->id);
});
