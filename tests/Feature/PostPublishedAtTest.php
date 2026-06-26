<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it sets published_at when status changes to published', function () {
    $user = User::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $user->id,
        'status' => 'draft',
        'published_at' => null,
    ]);

    expect($post->published_at)->toBeNull();

    $post->update(['status' => 'published']);

    $post->refresh();
    expect($post->status)->toBe('published');
    expect($post->published_at)->not->toBeNull();
    expect($post->published_at->isToday())->toBeTrue();
});

test('it does not overwrite published_at if already set when publishing', function () {
    $user = User::factory()->create();
    $pastDate = now()->subDays(5);

    $post = Post::factory()->create([
        'user_id' => $user->id,
        'status' => 'draft',
        'published_at' => $pastDate,
    ]);

    $post->update(['status' => 'published']);

    $post->refresh();
    // Debería mantener la fecha original si ya estaba seteada (por ejemplo, publicación programada manual)
    expect($post->published_at->toDateTimeString())->toBe($pastDate->toDateTimeString());
});
