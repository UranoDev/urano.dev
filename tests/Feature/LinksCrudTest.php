<?php

use App\Enums\LinkType;
use App\Models\Link;
use App\Models\LinkClick;
use App\Models\Post;use App\Models\User;
use Livewire\Livewire;

// --- Acceso ---

test('un administrador puede acceder a la pagina de links', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('links.index'))
        ->assertOk();
});

test('un author no puede acceder a la pagina de links', function () {
    $author = User::factory()->author()->create();

    $this->actingAs($author)
        ->get(route('links.index'))
        ->assertForbidden();
});

test('un visitor no puede acceder a la pagina de links', function () {
    $visitor = User::factory()->visitor()->create();

    $this->actingAs($visitor)
        ->get(route('links.index'))
        ->assertRedirectToRoute('home');
});

test('un usuario no autenticado es redirigido al login', function () {
    $this->get(route('links.index'))
        ->assertRedirect(route('login'));
});

// --- Crear ---

test('un administrador puede crear un link externo', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('openCreate')
        ->set('title', 'Mi sitio web')
        ->set('type', 'external')
        ->set('url', 'https://example.com')
        ->set('sortOrder', 1)
        ->call('save');

    $this->assertDatabaseHas('links', [
        'title' => 'Mi sitio web',
        'url' => 'https://example.com',
        'type' => LinkType::External->value,
        'sort_order' => 1,
        'is_active' => true,
    ]);
});

test('un administrador puede crear un link interno', function () {
    $admin = User::factory()->admin()->create();
    $post = Post::factory()->create(['status' => 'published']);
    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('openCreate')
        ->set('title', 'Post interno')
        ->set('type', 'internal')
        ->set('postId', $post->id)        ->call('save');

    $this->assertDatabaseHas('links', [
        'title' => 'Post interno',
        'type' => LinkType::Internal->value,
        'url' => null,
        'post_id' => $post->id,    ]);
});

test('crear un link externo requiere url valida', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('openCreate')
        ->set('title', 'Sin url')
        ->set('type', 'external')
        ->set('url', '')
        ->call('save')
        ->assertHasErrors(['url']);
});

test('crear un link requiere titulo', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('openCreate')
        ->set('title', '')
        ->set('type', 'external')
        ->set('url', 'https://example.com')
        ->call('save')
        ->assertHasErrors(['title']);
});

test('crear un link externo con url invalida falla validacion', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('openCreate')
        ->set('title', 'Link roto')
        ->set('type', 'external')
        ->set('url', 'no-es-una-url')
        ->call('save')
        ->assertHasErrors(['url']);
});

// --- Editar ---

test('un administrador puede editar un link', function () {
    $admin = User::factory()->admin()->create();
    $link = Link::factory()->create(['title' => 'Titulo original', 'url' => 'https://original.com']);

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('openEdit', $link->id)
        ->assertSet('title', 'Titulo original')
        ->assertSet('url', 'https://original.com')
        ->set('title', 'Titulo editado')
        ->set('url', 'https://editado.com')
        ->call('save');

    $this->assertDatabaseHas('links', [
        'id' => $link->id,
        'title' => 'Titulo editado',
        'url' => 'https://editado.com',
    ]);
});

test('openEdit carga los datos del link en el formulario', function () {
    $admin = User::factory()->admin()->create();
    $link = Link::factory()->create([
        'title' => 'Mi link',
        'url' => 'https://ejemplo.com',
        'type' => LinkType::External,
        'is_active' => true,
        'sort_order' => 5,
    ]);

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('openEdit', $link->id)
        ->assertSet('editingId', $link->id)
        ->assertSet('title', 'Mi link')
        ->assertSet('url', 'https://ejemplo.com')
        ->assertSet('type', 'external')
        ->assertSet('isActive', true)
        ->assertSet('sortOrder', 5);
});

// --- Activar / Desactivar ---

test('un administrador puede desactivar un link activo', function () {
    $admin = User::factory()->admin()->create();
    $link = Link::factory()->create(['is_active' => true]);

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('toggleActive', $link->id);

    expect($link->fresh()->is_active)->toBeFalse();
});

test('un administrador puede activar un link inactivo', function () {
    $admin = User::factory()->admin()->create();
    $link = Link::factory()->inactive()->create();

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('toggleActive', $link->id);

    expect($link->fresh()->is_active)->toBeTrue();
});

// --- Eliminar ---

test('un administrador puede eliminar un link', function () {
    $admin = User::factory()->admin()->create();
    $link = Link::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('confirmDelete', $link->id)
        ->assertSet('deletingId', $link->id)
        ->call('delete');

    $this->assertDatabaseMissing('links', ['id' => $link->id]);
});

test('al eliminar un link se eliminan sus clicks', function () {
    $admin = User::factory()->admin()->create();
    $link = Link::factory()->create();
    LinkClick::factory()->count(3)->create(['link_id' => $link->id]);

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('confirmDelete', $link->id)
        ->call('delete');

    expect(LinkClick::where('link_id', $link->id)->count())->toBe(0);
});

// --- Listado ---

test('la pagina de links muestra todos los links', function () {
    $admin = User::factory()->admin()->create();
    Link::factory()->create(['title' => 'Primer link', 'sort_order' => 1]);
    Link::factory()->create(['title' => 'Segundo link', 'sort_order' => 2]);

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->assertSeeText('Primer link')
        ->assertSeeText('Segundo link');
});

test('la pagina de links muestra los clicks de cada link', function () {
    $admin = User::factory()->admin()->create();
    $link = Link::factory()->create(['title' => 'Link con clicks']);
    LinkClick::factory()->count(5)->create(['link_id' => $link->id]);

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->assertSeeText('5');
});

// --- Tipo interno limpia la URL ---

test('cambiar tipo a interno limpia la url', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('openCreate')
        ->set('url', 'https://example.com')
        ->set('type', 'internal')
        ->assertSet('url', null);
});

// --- Reordenamiento ---

test('handleSort mueve un link a una nueva posicion', function () {
    $admin = User::factory()->admin()->create();
    $link1 = Link::factory()->create(['sort_order' => 0]);
    $link2 = Link::factory()->create(['sort_order' => 1]);
    $link3 = Link::factory()->create(['sort_order' => 2]);

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('handleSort', $link3->id, 0);

    expect($link3->fresh()->sort_order)->toBe(0);
    expect($link1->fresh()->sort_order)->toBe(1);
    expect($link2->fresh()->sort_order)->toBe(2);
});

test('handleSort mueve un link al final', function () {
    $admin = User::factory()->admin()->create();
    $link1 = Link::factory()->create(['sort_order' => 0]);
    $link2 = Link::factory()->create(['sort_order' => 1]);
    $link3 = Link::factory()->create(['sort_order' => 2]);

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('handleSort', $link1->id, 2);

    expect($link2->fresh()->sort_order)->toBe(0);
    expect($link3->fresh()->sort_order)->toBe(1);
    expect($link1->fresh()->sort_order)->toBe(2);
});

test('handleSort mueve un link al medio', function () {
    $admin = User::factory()->admin()->create();
    $link1 = Link::factory()->create(['sort_order' => 0]);
    $link2 = Link::factory()->create(['sort_order' => 1]);
    $link3 = Link::factory()->create(['sort_order' => 2]);

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('handleSort', $link3->id, 1);

    expect($link1->fresh()->sort_order)->toBe(0);
    expect($link3->fresh()->sort_order)->toBe(1);
    expect($link2->fresh()->sort_order)->toBe(2);
});

test('al abrir el formulario de crear el orden se asigna automaticamente al final', function () {
    $admin = User::factory()->admin()->create();
    Link::factory()->create(['sort_order' => 5]);
    Link::factory()->create(['sort_order' => 3]);

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('openCreate')
        ->assertSet('sortOrder', 6);
});

test('crear un link interno requiere un post_id valido', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('openCreate')
        ->set('title', 'Post interno invalido')
        ->set('type', 'internal')
        ->set('postId', null)
        ->call('save')
        ->assertHasErrors(['postId']);

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('openCreate')
        ->set('title', 'Post interno inexistente')
        ->set('type', 'internal')
        ->set('postId', 99999)
        ->call('save')
        ->assertHasErrors(['postId']);
});

test('un administrador puede editar un link interno para cambiar el post asociado', function () {
    $admin = User::factory()->admin()->create();
    $post1 = Post::factory()->create(['status' => 'published']);
    $post2 = Post::factory()->create(['status' => 'published']);
    $link = Link::factory()->internal()->create([
        'title' => 'Link de post',
        'post_id' => $post1->id,
    ]);

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('openEdit', $link->id)
        ->assertSet('postId', $post1->id)
        ->set('postId', $post2->id)
        ->call('save');

    expect($link->fresh()->post_id)->toBe($post2->id);
});

test('al abrir el formulario de crear el owner predeterminado es el admin autenticado', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::links.index')
        ->call('openCreate')
        ->assertSet('userId', $admin->id);
});

test('un administrador puede asignar y cambiar el owner del link', function () {
    $admin1 = User::factory()->admin()->create(['name' => 'Admin Uno']);
    $admin2 = User::factory()->admin()->create(['name' => 'Admin Dos']);

    // Crear link asignando a admin2 como owner
    Livewire::actingAs($admin1)
        ->test('pages::links.index')
        ->call('openCreate')
        ->set('title', 'Link con owner')
        ->set('type', 'external')
        ->set('url', 'https://example.com')
        ->set('userId', $admin2->id)
        ->call('save');

    $this->assertDatabaseHas('links', [
        'title' => 'Link con owner',
        'user_id' => $admin2->id,
    ]);

    $link = Link::where('title', 'Link con owner')->first();

    // Editar link asignando de vuelta a admin1 como owner
    Livewire::actingAs($admin1)
        ->test('pages::links.index')
        ->call('openEdit', $link->id)
        ->assertSet('userId', $admin2->id)
        ->set('userId', $admin1->id)
        ->call('save');

    expect($link->fresh()->user_id)->toBe($admin1->id);
});