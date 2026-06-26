<?php

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Services\PostStaticGenerator;use Illuminate\Support\Facades\File;

test('it displays a published post from its static html file', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Static Post',
        'slug' => 'static-post',
        'content' => '# My Static Content',
        'status' => 'published',
    ]);

    // El observer debería haber generado el archivo
    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'static_path' => 'posts/static-post.html',
    ]);

    $response = $this->get(route('blog.show', 'static-post'));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/html; charset=UTF-8');

    // Usamos false para que no escape el contenido en la comparación
    $response->assertSee('Static Post', false);
    $response->assertSee('My Static Content', false);
});

test('it returns 404 for a draft post', function () {
    $user = User::factory()->create();
    Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Draft Post',
        'slug' => 'draft-post',
        'status' => 'draft',
    ]);

    $response = $this->get(route('blog.show', 'draft-post'));

    $response->assertStatus(404);
});

test('it returns 404 for a non-existent slug', function () {
    $response = $this->get(route('blog.show', 'non-existent-slug'));

    $response->assertStatus(404);
});

test('it returns 404 if the static file is missing', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Missing File Post',
        'slug' => 'missing-file-post',
        'status' => 'published',
    ]);

    // Borramos el archivo manualmente para simular el error
    $path = storage_path('app/public/'.$post->static_path);
    if (File::exists($path)) {
        File::delete($path);
    }

    $response = $this->get(route('blog.show', 'missing-file-post'));

    $response->assertStatus(404);
});

test('it displays a list of published posts sorted by published_at desc', function () {
    $user = User::factory()->create();

    $post1 = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'First Published Post',
        'slug' => 'first-published',
        'status' => 'published',
        'published_at' => now()->subDays(2),
    ]);

    $post2 = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Second Published Post',
        'slug' => 'second-published',
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);

    $draftPost = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Draft Post In List',
        'slug' => 'draft-post-list',
        'status' => 'draft',
    ]);

    $response = $this->get(route('blog.index'));

    $response->assertStatus(200);
    $response->assertSee('Second Published Post');
    $response->assertSee('First Published Post');
    $response->assertDontSee('Draft Post In List');

    // Confirm sorting (second-published is newer, so it should appear first)
    $response->assertSeeInOrder([
        'Second Published Post',
        'First Published Post',
    ]);
});

test('tags are displayed in the blog listing', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Post con Tags',
        'status' => 'published',
        'published_at' => now(),
    ]);
    $tag = Tag::factory()->create(['name' => 'laravel']);
    $post->tags()->sync([$tag->id]);

    $response = $this->get(route('blog.index'));

    $response->assertStatus(200);
    $response->assertSee('#laravel');
});

test('tags are baked into the static html when post is published', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Post Estatico con Tags',
        'slug' => 'post-estatico-con-tags',
        'content' => 'Contenido del post',
        'status' => 'published',
    ]);
    $tag = Tag::factory()->create(['name' => 'php-test']);
    $post->tags()->sync([$tag->id]);

    // Regenerate static HTML now that tags are associated
    $post->load('tags', 'author');
    $staticPath = app(PostStaticGenerator::class)->generate($post);
    $post->withoutEvents(fn () => $post->update(['static_path' => $staticPath]));

    $response = $this->get(route('blog.show', 'post-estatico-con-tags'));

    $response->assertStatus(200);
    $response->assertSee('#php-test', false);
});

test('it displays an empty state when there are no published posts', function () {
    // Delete all posts
    Post::query()->delete();

    $response = $this->get(route('blog.index'));

    $response->assertStatus(200);
    $response->assertSee('No hay publicaciones disponibles por el momento.');
});