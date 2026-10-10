<?php

use App\Models\ProjectInquiry;
use App\Models\User;
use Livewire\Livewire;

// --- Acceso ---

test('un administrador puede acceder a la pagina de contactos', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('inquiries.index'))
        ->assertOk();
});

test('un author no puede acceder a la pagina de contactos', function () {
    $author = User::factory()->author()->create();

    $this->actingAs($author)
        ->get(route('inquiries.index'))
        ->assertForbidden();
});

test('un usuario no autenticado es redirigido al login', function () {
    $this->get(route('inquiries.index'))
        ->assertRedirect(route('login'));
});

// --- Listado ---

test('la pagina muestra lo que dejaron los visitantes', function () {
    $admin = User::factory()->admin()->create();

    ProjectInquiry::factory()->create([
        'name' => 'Ana López',
        'email' => 'ana@example.com',
        'company' => 'Hotel La Piedra',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::inquiries.index')
        ->assertSee('Ana López')
        ->assertSee('ana@example.com')
        ->assertSee('Hotel La Piedra');
});

test('el detalle muestra lo que el visitante escribio', function () {
    $admin = User::factory()->admin()->create();

    $inquiry = ProjectInquiry::factory()->create([
        'project_description' => 'Un sistema para controlar las salidas de tours y sus cupos.',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::inquiries.index')
        ->call('openDetail', $inquiry->id)
        ->assertSee('Un sistema para controlar las salidas de tours y sus cupos.');
});

// --- Eliminar ---

test('un administrador puede eliminar un contacto después de confirmar', function () {
    $admin = User::factory()->admin()->create();
    $spam = ProjectInquiry::factory()->create(['name' => 'ElmTLrQBovuJVJOnnI']);
    $real = ProjectInquiry::factory()->create(['name' => 'Ana López']);

    Livewire::actingAs($admin)
        ->test('pages::inquiries.index')
        ->call('confirmDelete', $spam->id)
        ->assertSet('deletingId', $spam->id)
        ->call('delete')
        ->assertSet('deletingId', null);

    $this->assertDatabaseMissing('project_inquiries', ['id' => $spam->id]);
    $this->assertDatabaseHas('project_inquiries', ['id' => $real->id]);
});

test('sin confirmar no se elimina ningún contacto', function () {
    $admin = User::factory()->admin()->create();
    $inquiry = ProjectInquiry::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::inquiries.index')
        ->call('delete');

    $this->assertDatabaseHas('project_inquiries', ['id' => $inquiry->id]);
});
