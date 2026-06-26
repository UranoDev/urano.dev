<?php

use App\Models\Link;
use App\Models\LinkClick;
use App\Models\Post;
test('hacer click en un link activo registra el click en la BD', function () {
    $link = Link::factory()->external()->create([
        'url' => 'https://example.com',
        'is_active' => true,
    ]);

    $this->get(route('links.click', $link));

    expect(LinkClick::where('link_id', $link->id)->count())->toBe(1);
});

test('hacer click en un link activo redirige a la url del link', function () {
    $link = Link::factory()->external()->create([
        'url' => 'https://example.com',
        'is_active' => true,
    ]);

    $this->get(route('links.click', $link))
        ->assertRedirect('https://example.com');
});

test('hacer click registra la ip del visitante', function () {
    $link = Link::factory()->external()->create([
        'url' => 'https://example.com',
        'is_active' => true,
    ]);

    $this->get(route('links.click', $link));

    $click = LinkClick::where('link_id', $link->id)->first();
    expect($click->ip_address)->not->toBeNull();
});

test('hacer click registra el user agent del visitante', function () {
    $link = Link::factory()->external()->create([
        'url' => 'https://example.com',
        'is_active' => true,
    ]);

    $this->withHeader('User-Agent', 'Test-Browser/1.0')
        ->get(route('links.click', $link));

    $click = LinkClick::where('link_id', $link->id)->first();
    expect($click->user_agent)->toBe('Test-Browser/1.0');
});

test('hacer click en un link inactivo devuelve 404', function () {
    $link = Link::factory()->inactive()->create();

    $this->get(route('links.click', $link))
        ->assertNotFound();
});

test('hacer click en un link inexistente devuelve 404', function () {
    $this->get(route('links.click', 99999))
        ->assertNotFound();
});

test('hacer click en un link interno sin url redirige al home', function () {
    $link = Link::factory()->internal()->create(['is_active' => true]);

    $this->get(route('links.click', $link))
        ->assertRedirect(route('home'));
});

test('hacer click en un link interno con post redirige a la url del archivo estatico', function () {
    $post = Post::factory()->create([
        'status' => 'published',
    ]);

    $link = Link::factory()->internal()->create([
        'is_active' => true,
        'post_id' => $post->id,
    ]);

    $this->get(route('links.click', $link))
        ->assertRedirect(asset('storage/'.$post->fresh()->static_path));
});
test('hacer click en un link interno sin url aun registra el click', function () {
    $link = Link::factory()->internal()->create(['is_active' => true]);

    $this->get(route('links.click', $link));

    expect(LinkClick::where('link_id', $link->id)->count())->toBe(1);
});

test('multiples clicks en el mismo link se registran todos', function () {
    $link = Link::factory()->external()->create([
        'url' => 'https://example.com',
        'is_active' => true,
    ]);

    $this->get(route('links.click', $link));
    $this->get(route('links.click', $link));
    $this->get(route('links.click', $link));

    expect(LinkClick::where('link_id', $link->id)->count())->toBe(3);
});
