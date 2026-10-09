<?php

use App\Enums\Role;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->storagePath = storage_path('app/public/posts');
    if (File::exists($this->storagePath)) {
        File::cleanDirectory($this->storagePath);
    }
});

test('it generates an html file when a post is published', function () {
    $user = User::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'My First Post',
        'slug' => 'my-first-post',
        'content' => '# Hello World',
        'status' => 'draft',
    ]);

    expect(File::exists($this->storagePath.'/my-first-post.html'))->toBeFalse();

    $post->update(['status' => 'published']);

    expect(File::exists($this->storagePath.'/my-first-post.html'))->toBeTrue();
    expect($post->fresh()->static_path)->toBe('posts/my-first-post.html');

    $htmlContent = File::get($this->storagePath.'/my-first-post.html');
    expect($htmlContent)->toContain('My First Post');
    expect($htmlContent)->toContain('<h1>Hello World</h1>');
});

test('it updates the html file when a published post is edited', function () {
    $user = User::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Original Title',
        'slug' => 'original-title',
        'content' => 'Original content',
        'status' => 'published',
    ]);

    expect(File::get($this->storagePath.'/original-title.html'))->toContain('Original Title');

    $post->update([
        'title' => 'Updated Title',
        'content' => 'Updated content',
    ]);

    $htmlContent = File::get($this->storagePath.'/original-title.html');
    expect($htmlContent)->toContain('Updated Title');
    expect($htmlContent)->toContain('<p>Updated content</p>');
});

test('it deletes the html file when a published post is deleted', function () {
    $user = User::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'To Be Deleted',
        'slug' => 'to-be-deleted',
        'status' => 'published',
    ]);

    expect(File::exists($this->storagePath.'/to-be-deleted.html'))->toBeTrue();

    $post->delete();

    expect(File::exists($this->storagePath.'/to-be-deleted.html'))->toBeFalse();
});

test('it deletes the html file when a published post is moved to draft', function () {
    $user = User::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Back to Draft',
        'slug' => 'back-to-draft',
        'status' => 'published',
    ]);

    expect(File::exists($this->storagePath.'/back-to-draft.html'))->toBeTrue();

    $post->update(['status' => 'draft']);

    expect(File::exists($this->storagePath.'/back-to-draft.html'))->toBeFalse();
    expect($post->fresh()->static_path)->toBeNull();
});

test('it renames the html file when the slug is updated', function () {
    $user = User::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Original Title',
        'slug' => 'old-slug',
        'status' => 'published',
    ]);

    expect(File::exists($this->storagePath.'/old-slug.html'))->toBeTrue();

    $post->update(['slug' => 'new-slug']);

    expect(File::exists($this->storagePath.'/old-slug.html'))->toBeFalse();
    expect(File::exists($this->storagePath.'/new-slug.html'))->toBeTrue();
    expect($post->fresh()->static_path)->toBe('posts/new-slug.html');
});

test('it generates static html using the site layout and is session-safe', function () {
    $author = User::factory()->create([
        'name' => 'Author User',
        'role' => Role::Author,
    ]);

    $loggedInUser = User::factory()->create([
        'name' => 'John Doe',
        'role' => Role::Admin,
    ]);

    // Act as the logged in admin who will publish the post
    $this->actingAs($loggedInUser);

    $post = Post::factory()->create([
        'user_id' => $author->id,
        'title' => 'My Beautiful Styled Post',
        'slug' => 'styled-post',
        'content' => '## Styled Subtitle',
        'status' => 'draft',
    ]);

    $post->update(['status' => 'published']);

    expect(File::exists($this->storagePath.'/styled-post.html'))->toBeTrue();

    $htmlContent = File::get($this->storagePath.'/styled-post.html');

    // It contains the site logo/branding and navigation
    expect($htmlContent)->toContain('Urano Dev');
    expect($htmlContent)->toContain('Inicio');
    expect($htmlContent)->toContain('Nosotros');
    expect($htmlContent)->toContain('Links');

    // It contains the post content and style rules
    expect($htmlContent)->toContain('My Beautiful Styled Post');
    expect($htmlContent)->toContain('<h2>Styled Subtitle</h2>');
    expect($htmlContent)->toContain('.post-content p');

    // It is session-safe (does NOT leak the admin\'s details even though we are logged in)
    expect($htmlContent)->not->toContain('Dashboard');
    expect($htmlContent)->not->toContain('Cerrar sesión');
    expect($htmlContent)->not->toContain('John Doe');
    expect($htmlContent)->toContain('Acceder');

    // It contains the author's details in the author section
    expect($htmlContent)->toContain('Author User');
});

test('it includes the author section with fallback initials and empty biography', function () {
    $author = User::factory()->create([
        'name' => 'Alice Smith',
        'avatar' => null,
        'bio' => null,
    ]);

    $post = Post::factory()->create([
        'user_id' => $author->id,
        'title' => 'Post by Alice',
        'slug' => 'post-by-alice',
        'content' => 'Content here',
        'status' => 'published',
    ]);

    expect(File::exists($this->storagePath.'/post-by-alice.html'))->toBeTrue();

    $htmlContent = File::get($this->storagePath.'/post-by-alice.html');

    expect($htmlContent)->toContain('data-test="author-section"');
    expect($htmlContent)->toContain('Alice Smith');
    expect($htmlContent)->toContain('AS'); // Initials fallback
    expect($htmlContent)->toContain('Sin biografía disponible.');
    expect($htmlContent)->not->toContain('data-test="author-avatar"');
    expect($htmlContent)->toContain('data-test="author-avatar-placeholder"');
});

test('it includes the author section with avatar and filled biography', function () {
    $author = User::factory()->create([
        'name' => 'Bob Johnson',
        'avatar' => 'avatars/bob.jpg',
        'bio' => 'A passionate software engineer and writer.',
    ]);

    $post = Post::factory()->create([
        'user_id' => $author->id,
        'title' => 'Post by Bob',
        'slug' => 'post-by-bob',
        'content' => 'Content here',
        'status' => 'published',
    ]);

    expect(File::exists($this->storagePath.'/post-by-bob.html'))->toBeTrue();

    $htmlContent = File::get($this->storagePath.'/post-by-bob.html');

    expect($htmlContent)->toContain('data-test="author-section"');
    expect($htmlContent)->toContain('Bob Johnson');
    expect($htmlContent)->toContain('A passionate software engineer and writer.');
    expect($htmlContent)->toContain('data-test="author-avatar"');
    expect($htmlContent)->toContain('storage/avatars/bob.jpg');
    expect($htmlContent)->not->toContain('data-test="author-avatar-placeholder"');
});

test('los enlaces del artículo se distinguen y cierra con un llamado a WhatsApp', function () {
    config(['services.whatsapp.number' => '525500000000']);

    $post = Post::factory()->create([
        'user_id' => User::factory()->create()->id,
        'title' => 'Post con enlace',
        'slug' => 'post-con-enlace',
        'content' => 'Lee [la guía](https://example.com).',
        'status' => 'published',
    ]);

    $htmlContent = File::get($this->storagePath.'/post-con-enlace.html');

    expect($htmlContent)->toContain('.post-content a {');
    expect($htmlContent)->toContain('data-test="post-whatsapp-cta"');
    expect($htmlContent)->toContain('https://wa.me/525500000000?text=');
    expect($htmlContent)->toContain(urlencode(route('blog.show', $post->slug)));
});

test('el comando posts:regenerate vuelve a generar los artículos publicados con los datos actuales', function () {
    $author = User::factory()->create(['bio' => null]);

    Post::factory()->create([
        'user_id' => $author->id,
        'slug' => 'publicado',
        'content' => 'Contenido',
        'status' => 'published',
    ]);
    Post::factory()->create([
        'user_id' => $author->id,
        'slug' => 'borrador',
        'content' => 'Contenido',
        'status' => 'draft',
    ]);

    $author->update(['bio' => 'Biografía nueva del autor.']);

    $this->artisan('posts:regenerate')
        ->expectsOutputToContain('1 artículo')
        ->assertSuccessful();

    expect(File::get($this->storagePath.'/publicado.html'))->toContain('Biografía nueva del autor.');
    expect(File::exists($this->storagePath.'/borrador.html'))->toBeFalse();
});

test('las etiquetas de vista previa del artículo llevan la dirección completa', function () {
    Post::factory()->create([
        'user_id' => User::factory()->create()->id,
        'slug' => 'con-vista-previa',
        'content' => 'Contenido',
        'cover_image' => 'posts/portada.png',
        'status' => 'published',
    ]);

    $htmlContent = File::get($this->storagePath.'/con-vista-previa.html');

    expect($htmlContent)->toContain('<link rel="canonical" href="'.url('/con-vista-previa').'">');
    expect($htmlContent)->toContain('<meta property="og:url" content="'.url('/con-vista-previa').'">');
    expect($htmlContent)->toContain('<meta property="og:image" content="'.url('/storage/posts/portada.png').'">');
    expect($htmlContent)->toContain('href="/blog"');
});
