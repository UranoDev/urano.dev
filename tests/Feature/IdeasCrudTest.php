<?php

use App\Enums\IdeaStatus;
use App\Models\Idea;
use App\Models\User;
use Livewire\Livewire;

// --- Acceso ---

test('un administrador puede acceder a la pagina de ideas', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('ideas.index'))
        ->assertOk();
});

test('un author no puede acceder a la pagina de ideas', function () {
    $author = User::factory()->author()->create();

    $this->actingAs($author)
        ->get(route('ideas.index'))
        ->assertForbidden();
});

test('un visitor no puede acceder a la pagina de ideas', function () {
    $visitor = User::factory()->visitor()->create();

    $this->actingAs($visitor)
        ->get(route('ideas.index'))
        ->assertRedirectToRoute('home');
});

test('un usuario no autenticado es redirigido al login', function () {
    $this->get(route('ideas.index'))
        ->assertRedirect(route('login'));
});

// --- Crear ---

test('un administrador puede crear una idea', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::ideas.index')
        ->call('openCreate')
        ->set('title', 'Mi nueva idea')
        ->set('body', 'Descripcion de la idea nueva')
        ->call('save');

    $this->assertDatabaseHas('ideas', [
        'title' => 'Mi nueva idea',
        'body' => 'Descripcion de la idea nueva',
        'user_id' => $admin->id,
        'status' => IdeaStatus::Pending->value,
    ]);
});

test('crear una idea requiere titulo y cuerpo', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::ideas.index')
        ->call('openCreate')
        ->set('title', '')
        ->set('body', '')
        ->call('save')
        ->assertHasErrors(['title', 'body']);
});

// --- Editar ---

test('un administrador puede editar una idea', function () {
    $admin = User::factory()->admin()->create();
    $idea = Idea::factory()->create(['title' => 'Titulo original', 'body' => 'Cuerpo original']);

    Livewire::actingAs($admin)
        ->test('pages::ideas.index')
        ->call('openEdit', $idea->id)
        ->assertSet('title', 'Titulo original')
        ->assertSet('body', 'Cuerpo original')
        ->set('title', 'Titulo editado')
        ->set('body', 'Cuerpo editado')
        ->call('save');

    $this->assertDatabaseHas('ideas', [
        'id' => $idea->id,
        'title' => 'Titulo editado',
        'body' => 'Cuerpo editado',
    ]);
});

// --- Aprobar / Rechazar ---

test('un administrador puede aprobar una idea pendiente', function () {
    $admin = User::factory()->admin()->create();
    $idea = Idea::factory()->pending()->create();

    Livewire::actingAs($admin)
        ->test('pages::ideas.index')
        ->call('approve', $idea->id);

    expect($idea->fresh()->status)->toBe(IdeaStatus::Approved);
});

test('un administrador puede rechazar una idea pendiente', function () {
    $admin = User::factory()->admin()->create();
    $idea = Idea::factory()->pending()->create();

    Livewire::actingAs($admin)
        ->test('pages::ideas.index')
        ->call('reject', $idea->id);

    expect($idea->fresh()->status)->toBe(IdeaStatus::Rejected);
});

// --- Eliminar ---

test('un administrador puede eliminar una idea', function () {
    $admin = User::factory()->admin()->create();
    $idea = Idea::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::ideas.index')
        ->call('confirmDelete', $idea->id)
        ->assertSet('deletingId', $idea->id)
        ->call('delete');

    $this->assertDatabaseMissing('ideas', ['id' => $idea->id]);
});

// --- Listado ---

test('la pagina de ideas muestra todas las ideas', function () {
    $admin = User::factory()->admin()->create();
    Idea::factory()->count(3)->create();

    Livewire::actingAs($admin)
        ->test('pages::ideas.index')
        ->assertSeeText(Idea::first()->title);
});
