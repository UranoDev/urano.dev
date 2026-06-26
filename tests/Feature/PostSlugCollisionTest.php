<?php

use App\Enums\Role;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('it suggests a unique slug if the generated one already exists', function () {
    $user = User::factory()->create(['role' => Role::Author]);

    // Crear un post existente
    Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Test Post',
        'slug' => 'test-post',
    ]);

    // Intentar crear otro con el mismo título (que generaría el mismo slug)
    $component = Livewire::actingAs($user)
        ->test('pages::posts.form')
        ->set('title', 'Test Post')
        ->set('content', 'Content')
        ->set('status', 'draft')
        ->call('save');

    $component->assertHasNoErrors();

    // El segundo post debería tener un slug como 'test-post-1'
    $this->assertDatabaseHas('posts', [
        'title' => 'Test Post',
        'slug' => 'test-post-1',
    ]);
});

test('it suggests a unique slug if the manually entered one already exists', function () {
    $user = User::factory()->create(['role' => Role::Author]);

    // Crear un post con slug manual
    Post::factory()->create([
        'user_id' => $user->id,
        'slug' => 'manual-slug',
    ]);

    // Intentar crear otro con el mismo slug manual
    Livewire::actingAs($user)
        ->test('pages::posts.form')
        ->set('title', 'Another Post')
        ->set('slug', 'manual-slug')
        ->set('content', 'Content')
        ->set('status', 'draft')
        ->call('save')
        ->assertHasNoErrors();

    // Aquí el comportamiento deseado según el issue es que sugiera uno nuevo
    // Vamos a verificar que se guardó con el sufijo.
    $this->assertDatabaseHas('posts', [
        'slug' => 'manual-slug-1',
    ]);
});

test('it handles multiple collisions', function () {
    $user = User::factory()->create(['role' => Role::Author]);

    Post::factory()->create(['user_id' => $user->id, 'slug' => 'collision']);
    Post::factory()->create(['user_id' => $user->id, 'slug' => 'collision-1']);

    Livewire::actingAs($user)
        ->test('pages::posts.form')
        ->set('title', 'New')
        ->set('slug', 'collision')
        ->set('content', 'Content')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('posts', [
        'slug' => 'collision-2',
    ]);
});

test('it prevents collision with reserved routes', function () {
    $user = User::factory()->create(['role' => Role::Author]);

    Livewire::actingAs($user)
        ->test('pages::posts.form')
        ->set('title', 'About')
        ->set('slug', 'about')
        ->set('content', 'Content')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('posts', [
        'slug' => 'about-1',
    ]);
});

test('it does not change slug when updating the same post', function () {
    $user = User::factory()->create(['role' => Role::Author]);
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Stable Post',
        'slug' => 'stable-post',
    ]);

    Livewire::actingAs($user)
        ->test('pages::posts.form', ['post' => $post])
        ->set('title', 'Stable Post Updated') // Título cambia, pero queremos mantener slug si no se edita manual
        ->call('save')
        ->assertHasNoErrors();

    $post->refresh();
    expect($post->slug)->toBe('stable-post');
});
