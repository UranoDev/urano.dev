<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->storagePath = storage_path('app/public/posts');
    if (File::exists($this->storagePath)) {
        File::deleteDirectory($this->storagePath);
    }
});

test('it publishes scheduled posts whose published_at is in the past', function () {
    $user = User::factory()->create();

    // Past scheduled post
    $scheduledPast = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Scheduled Past Post',
        'slug' => 'scheduled-past',
        'content' => 'Content here',
        'status' => 'scheduled',
        'published_at' => now()->subMinute(),
    ]);

    // Future scheduled post
    $scheduledFuture = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Scheduled Future Post',
        'slug' => 'scheduled-future',
        'content' => 'Content here',
        'status' => 'scheduled',
        'published_at' => now()->addHour(),
    ]);

    // Past draft post
    $draftPast = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Draft Past Post',
        'slug' => 'draft-past',
        'content' => 'Content here',
        'status' => 'draft',
        'published_at' => now()->subMinute(),
    ]);

    // Run the scheduler command
    Artisan::call('posts:publish-scheduled');

    // Past scheduled post should be published and its static file generated
    expect($scheduledPast->fresh()->status)->toBe('published');
    expect($scheduledPast->fresh()->static_path)->toBe('posts/scheduled-past.html');
    expect(File::exists($this->storagePath.'/scheduled-past.html'))->toBeTrue();

    // Future scheduled post should remain scheduled
    expect($scheduledFuture->fresh()->status)->toBe('scheduled');
    expect($scheduledFuture->fresh()->static_path)->toBeNull();
    expect(File::exists($this->storagePath.'/scheduled-future.html'))->toBeFalse();

    // Past draft should remain draft
    expect($draftPast->fresh()->status)->toBe('draft');
    expect($draftPast->fresh()->static_path)->toBeNull();
    expect(File::exists($this->storagePath.'/draft-past.html'))->toBeFalse();
});
