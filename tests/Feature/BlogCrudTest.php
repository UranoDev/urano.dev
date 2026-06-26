<?php

use App\Enums\Role;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guest cannot access blog dashboard', function () {
    $this->get(route('posts.index'))
        ->assertRedirect(route('login'));
});

test('visitor cannot access blog dashboard', function () {
    $user = User::factory()->create(['role' => Role::Visitor]);
    $this->actingAs($user)
        ->get(route('posts.index'))
        ->assertRedirect(route('home'));
});

test('author can see only their own posts', function () {
    $author = User::factory()->create(['role' => Role::Author]);
    $otherAuthor = User::factory()->create(['role' => Role::Author]);

    $post1 = Post::factory()->create(['user_id' => $author->id, 'title' => 'Author Post']);
    $post2 = Post::factory()->create(['user_id' => $otherAuthor->id, 'title' => 'Other Post']);

    Livewire::actingAs($author)
        ->test('pages::posts.index')
        ->assertSee('Author Post')
        ->assertDontSee('Other Post');
});

test('admin can see all posts', function () {
    $admin = User::factory()->create(['role' => Role::Admin]);
    $author = User::factory()->create(['role' => Role::Author]);

    Post::factory()->create(['user_id' => $admin->id, 'title' => 'Admin Post']);
    Post::factory()->create(['user_id' => $author->id, 'title' => 'Author Post']);

    Livewire::actingAs($admin)
        ->test('pages::posts.index')
        ->assertSee('Admin Post')
        ->assertSee('Author Post');
});

test('author can create a post', function () {
    $author = User::factory()->create(['role' => Role::Author]);

    Livewire::actingAs($author)
        ->test('pages::posts.form')
        ->set('title', 'New Post Title')
        ->set('content', 'Post content here')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('posts.index'));

    $this->assertDatabaseHas('posts', [
        'title' => 'New Post Title',
        'user_id' => $author->id,
    ]);
});

test('posts index links to the post form pages', function () {
    $author = User::factory()->create(['role' => Role::Author]);
    $post = Post::factory()->create(['user_id' => $author->id, 'title' => 'Editable Post']);

    Livewire::actingAs($author)
        ->test('pages::posts.index')
        ->assertSee(route('posts.create'), false)
        ->assertSee(route('posts.edit', $post), false);
});

test('author cannot edit others posts', function () {
    $author = User::factory()->create(['role' => Role::Author]);
    $otherAuthor = User::factory()->create(['role' => Role::Author]);
    $post = Post::factory()->create(['user_id' => $otherAuthor->id]);

    $this->actingAs($author)
        ->get(route('posts.edit', $post))
        ->assertForbidden();
});

test('admin can edit any post', function () {
    $admin = User::factory()->create(['role' => Role::Admin]);
    $author = User::factory()->create(['role' => Role::Author]);
    $post = Post::factory()->create(['user_id' => $author->id]);

    Livewire::actingAs($admin)
        ->test('pages::posts.form', ['post' => $post])
        ->set('title', 'Updated by Admin')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('posts.index'));

    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'title' => 'Updated by Admin',
    ]);
});

test('it allows uploading a cover image when creating a post', function () {
    Storage::fake('public');

    $author = User::factory()->create(['role' => Role::Author]);
    $file = UploadedFile::fake()->image('cover.jpg');

    Livewire::actingAs($author)
        ->test('pages::posts.form')
        ->set('title', 'New Post With Cover')
        ->set('content', 'Post content here')
        ->set('cover_image_file', $file)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('posts.index'));

    $post = Post::where('title', 'New Post With Cover')->first();
    expect($post->cover_image)->not->toBeNull();
    Storage::disk('public')->assertExists($post->cover_image);
});

test('tags are synced when creating a post', function () {
    $author = User::factory()->create(['role' => Role::Author]);

    Livewire::actingAs($author)
        ->test('pages::posts.form')
        ->set('title', 'Post con Etiquetas')
        ->set('content', 'Contenido')
        ->call('addTag', 'laravel')
        ->call('addTag', 'php')
        ->call('save')
        ->assertHasNoErrors();

    $post = Post::where('title', 'Post con Etiquetas')->firstOrFail();
    expect($post->tags()->pluck('name')->toArray())->toContain('laravel', 'php');
});

test('tags are loaded when editing a post', function () {
    $author = User::factory()->create(['role' => Role::Author]);
    $post = Post::factory()->create(['user_id' => $author->id]);
    $tag = Tag::create(['name' => 'vue-load-test', 'slug' => 'vue-load-test']);
    $post->tags()->sync([$tag->id]);

    Livewire::actingAs($author)
        ->test('pages::posts.form', ['post' => $post])
        ->assertSet('selectedTags', ['vue-load-test']);
});

test('tags can be removed from a post', function () {
    $author = User::factory()->create(['role' => Role::Author]);
    $post = Post::factory()->create(['user_id' => $author->id]);
    $tag1 = Tag::create(['name' => 'css-remove-test', 'slug' => 'css-remove-test']);
    $tag2 = Tag::create(['name' => 'js-remove-test', 'slug' => 'js-remove-test']);
    $post->tags()->sync([$tag1->id, $tag2->id]);

    Livewire::actingAs($author)
        ->test('pages::posts.form', ['post' => $post])
        ->call('removeTag', 'css-remove-test')
        ->call('save')
        ->assertHasNoErrors();

    expect($post->fresh()->tags()->pluck('name')->toArray())
        ->toContain('js-remove-test')
        ->not->toContain('css-remove-test');
});

test('adding duplicate tag is ignored', function () {
    $author = User::factory()->create(['role' => Role::Author]);

    $component = Livewire::actingAs($author)
        ->test('pages::posts.form')
        ->call('addTag', 'laravel')
        ->call('addTag', 'laravel');

    expect($component->get('selectedTags'))->toBe(['laravel']);
});

test('it allows removing a cover image when editing a post', function () {
    Storage::fake('public');

    $author = User::factory()->create(['role' => Role::Author]);
    $file = UploadedFile::fake()->image('old_cover.jpg');

    // Store old cover image
    $coverImagePath = $file->store('images', 'public');
    Storage::disk('public')->assertExists($coverImagePath);

    $post = Post::factory()->create([
        'user_id' => $author->id,
        'title' => 'Post With Image',
        'cover_image' => $coverImagePath,
    ]);

    Livewire::actingAs($author)
        ->test('pages::posts.form', ['post' => $post])
        ->assertSet('cover_image', $coverImagePath)
        ->call('removeCurrentImage')
        ->call('save')
        ->assertHasNoErrors();

    expect($post->fresh()->cover_image)->toBeNull();
    Storage::disk('public')->assertMissing($coverImagePath);
});