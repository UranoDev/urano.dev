<?php

use App\Enums\LinkType;
use App\Models\Link;
use App\Models\LinkClick;

// --- Link: factory y valores por defecto ---

test('un link nuevo es externo y activo por defecto', function () {
    $link = Link::factory()->create();

    expect($link->type)->toBe(LinkType::External)
        ->and($link->is_active)->toBeTrue()
        ->and($link->isExternal())->toBeTrue()
        ->and($link->isInternal())->toBeFalse();
});

test('se puede crear un link interno', function () {
    $link = Link::factory()->internal()->create();

    expect($link->type)->toBe(LinkType::Internal)
        ->and($link->isInternal())->toBeTrue()
        ->and($link->isExternal())->toBeFalse();
});

test('se puede crear un link inactivo', function () {
    $link = Link::factory()->inactive()->create();

    expect($link->is_active)->toBeFalse();
});

// --- Link: relación con clicks ---

test('un link tiene muchos clicks', function () {
    $link = Link::factory()->create();
    LinkClick::factory()->count(3)->create(['link_id' => $link->id]);

    expect($link->clicks)->toHaveCount(3);
});

test('al eliminar un link se eliminan sus clicks', function () {
    $link = Link::factory()->create();
    LinkClick::factory()->count(2)->create(['link_id' => $link->id]);

    $link->delete();

    expect(LinkClick::where('link_id', $link->id)->count())->toBe(0);
});

// --- LinkClick: relación con link ---

test('un click pertenece a un link', function () {
    $link = Link::factory()->create();
    $click = LinkClick::factory()->create(['link_id' => $link->id]);

    expect($click->link->id)->toBe($link->id);
});

// --- LinkClick: campos opcionales ---

test('un click puede guardarse con ip y user agent', function () {
    $link = Link::factory()->create();

    $click = LinkClick::factory()->create([
        'link_id' => $link->id,
        'ip_address' => '192.168.1.1',
        'user_agent' => 'Mozilla/5.0',
    ]);

    expect($click->ip_address)->toBe('192.168.1.1')
        ->and($click->user_agent)->toBe('Mozilla/5.0');
});

test('un click puede guardarse sin ip ni user agent', function () {
    $link = Link::factory()->create();

    $click = LinkClick::factory()->create([
        'link_id' => $link->id,
        'ip_address' => null,
        'user_agent' => null,
    ]);

    expect($click->ip_address)->toBeNull()
        ->and($click->user_agent)->toBeNull();
});

// --- Link: sort_order ---

test('un link nuevo tiene sort_order en cero por defecto', function () {
    $link = Link::factory()->create(['sort_order' => 0]);

    expect($link->sort_order)->toBe(0);
});

test('se pueden recuperar links ordenados por sort_order', function () {
    Link::factory()->create(['sort_order' => 3, 'title' => 'Tercero']);
    Link::factory()->create(['sort_order' => 1, 'title' => 'Primero']);
    Link::factory()->create(['sort_order' => 2, 'title' => 'Segundo']);

    $links = Link::orderBy('sort_order')->get();

    expect($links->first()->title)->toBe('Primero')
        ->and($links->last()->title)->toBe('Tercero');
});

// --- Link: post_id para links internos ---

test('un link interno puede tener post_id', function () {
    $link = Link::factory()->internal()->create(['post_id' => 42]);

    expect($link->post_id)->toBe(42);
});

test('un link externo tiene post_id nulo', function () {
    $link = Link::factory()->external()->create();

    expect($link->post_id)->toBeNull();
});
