<?php

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a post can be created', function () {
    $user = User::factory()->create();

    $post = Post::create([
        'user_id' => $user->id,
        'title' => 'Mi primer post',
        'slug' => 'mi-primer-post',
        'content' => '# Hola Mundo\nEste es un post en markdown.',
        'status' => 'draft',
    ]);

    expect($post->title)->toBe('Mi primer post');
    expect($post->author->id)->toBe($user->id);
    $this->assertDatabaseHas('posts', [
        'title' => 'Mi primer post',
        'slug' => 'mi-primer-post',
    ]);
});

test('a post can have tags', function () {
    $post = Post::factory()->create();
    $tags = Tag::factory()->count(3)->create();

    $post->tags()->attach($tags);

    expect($post->tags)->toHaveCount(3);
    expect($tags->first()->posts)->toHaveCount(1);

    foreach ($tags as $tag) {
        $this->assertDatabaseHas('post_tag', [
            'post_id' => $post->id,
            'tag_id' => $tag->id,
        ]);
    }
});

test('a post can be in different statuses', function () {
    $statuses = ['draft', 'published', 'scheduled', 'archived'];

    foreach ($statuses as $status) {
        $post = Post::factory()->create(['status' => $status]);
        expect($post->status)->toBe($status);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => $status,
        ]);
    }
});
