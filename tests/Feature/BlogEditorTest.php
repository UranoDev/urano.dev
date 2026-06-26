<?php

use App\Enums\Role;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('blog editor can save content with code blocks', function () {
    $author = User::factory()->create(['role' => Role::Author]);

    $codeBlock = "```php\n<?php\necho 'Hello World';\n```";

    Livewire::actingAs($author)
        ->test('pages::posts.form')
        ->set('title', 'Post with Code')
        ->set('content', $codeBlock)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('posts.index'));

    $post = Post::where('title', 'Post with Code')->first();

    expect($post->content)->toBe($codeBlock);
});

test('it fails validation when content is empty', function () {
    $author = User::factory()->create(['role' => Role::Author]);

    Livewire::actingAs($author)
        ->test('pages::posts.form')
        ->set('title', 'Post without content')
        ->set('content', '')
        ->call('save')
        ->assertHasErrors(['content' => 'required']);
});

test('markdown editor component is modelable', function () {
    Livewire::test('ui.markdown-editor', ['value' => 'Initial'])
        ->assertSet('value', 'Initial')
        ->set('value', 'Updated')
        ->assertSet('value', 'Updated');
});

test('markdown editor renders toast ui integration', function () {
    Livewire::test('ui.markdown-editor', ['value' => 'Initial'])
        ->assertSeeHtml('waitForToastEditor')
        ->assertSeeHtml('window.toastui?.Editor')
        ->assertSeeHtml('getMarkdown')
        ->assertSeeHtml('setMarkdown')
        ->assertSeeHtml('wire:ignore')
        ->assertDontSeeHtml('EasyMDE');
});

test('content persists after save and edit', function () {
    $author = User::factory()->create(['role' => Role::Author]);
    $post = Post::factory()->create([
        'user_id' => $author->id,
        'title' => 'Initial Title',
        'content' => 'Initial Content',
        'status' => 'published',
    ]);

    Livewire::actingAs($author)
        ->test('pages::posts.form', ['post' => $post])
        ->assertSet('content', 'Initial Content')
        ->set('content', 'Modified Content')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('posts.index'));

    $post->refresh();
    expect($post->content)->toBe('Modified Content');

    // Simulate refresh by opening edit again
    Livewire::actingAs($author)
        ->test('pages::posts.form', ['post' => $post])
        ->assertSet('content', 'Modified Content');
});

test('successful save redirects to posts index', function () {
    $author = User::factory()->create(['role' => Role::Author]);

    Livewire::actingAs($author)
        ->test('pages::posts.form')
        ->set('title', 'New Post')
        ->set('content', 'Some content')
        ->call('save')
        ->assertRedirect(route('posts.index'));
});
