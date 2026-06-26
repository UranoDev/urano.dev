<?php

use App\Enums\Role;
use App\Models\User;
use Livewire\Livewire;

// --- Acceso ---

test('un administrador puede acceder a la pagina de usuarios', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('users.index'))
        ->assertOk();
});

test('un author no puede acceder a la pagina de usuarios', function () {
    $author = User::factory()->author()->create();

    $this->actingAs($author)
        ->get(route('users.index'))
        ->assertForbidden();
});

test('un visitor no puede acceder a la pagina de usuarios', function () {
    $visitor = User::factory()->visitor()->create();

    $this->actingAs($visitor)
        ->get(route('users.index'))
        ->assertRedirectToRoute('home');
});

test('un usuario no autenticado es redirigido al login', function () {
    $this->get(route('users.index'))
        ->assertRedirect(route('login'));
});

// --- Listado ---

test('la pagina muestra autores y administradores pero no visitantes', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->author()->create(['name' => 'Autor Listado']);
    User::factory()->admin()->create(['name' => 'Admin Listado']);
    User::factory()->visitor()->create(['name' => 'Visitor Oculto']);

    Livewire::actingAs($admin)
        ->test('pages::users.index')
        ->assertSeeText('Autor Listado')
        ->assertSeeText('Admin Listado')
        ->assertDontSeeText('Visitor Oculto');
});

test('la busqueda filtra por nombre o email', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->author()->create(['name' => 'Juan Perez', 'email' => 'juan@example.com']);
    User::factory()->author()->create(['name' => 'Maria Lopez', 'email' => 'maria@example.com']);

    Livewire::actingAs($admin)
        ->test('pages::users.index')
        ->set('search', 'Juan')
        ->assertSeeText('Juan Perez')
        ->assertDontSeeText('Maria Lopez');
});

// --- Crear ---

test('un administrador puede crear un autor', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::users.index')
        ->call('openCreate')
        ->set('name', 'Nuevo Autor')
        ->set('email', 'nuevo@example.com')
        ->set('password', 'secretpassword')
        ->set('role', 'author')
        ->call('save');

    $this->assertDatabaseHas('users', [
        'name' => 'Nuevo Autor',
        'email' => 'nuevo@example.com',
        'role' => Role::Author->value,
        'is_active' => true,
    ]);
});

test('un administrador puede crear otro administrador', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::users.index')
        ->call('openCreate')
        ->set('name', 'Nuevo Admin')
        ->set('email', 'nuevoadmin@example.com')
        ->set('password', 'secretpassword')
        ->set('role', 'admin')
        ->call('save');

    $this->assertDatabaseHas('users', [
        'name' => 'Nuevo Admin',
        'email' => 'nuevoadmin@example.com',
        'role' => Role::Admin->value,
    ]);
});

test('crear un usuario requiere nombre email y password', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::users.index')
        ->call('openCreate')
        ->set('name', '')
        ->set('email', '')
        ->set('password', '')
        ->call('save')
        ->assertHasErrors(['name', 'email', 'password']);
});

test('crear un usuario requiere email unico', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->author()->create(['email' => 'existente@example.com']);

    Livewire::actingAs($admin)
        ->test('pages::users.index')
        ->call('openCreate')
        ->set('name', 'Otro')
        ->set('email', 'existente@example.com')
        ->set('password', 'secretpassword')
        ->call('save')
        ->assertHasErrors(['email']);
});

test('el rol solo puede ser author o admin', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::users.index')
        ->call('openCreate')
        ->set('name', 'Test')
        ->set('email', 'test@example.com')
        ->set('password', 'secretpassword')
        ->set('role', 'visitor')
        ->call('save')
        ->assertHasErrors(['role']);
});

// --- Editar ---

test('un administrador puede editar un usuario', function () {
    $admin = User::factory()->admin()->create();
    $author = User::factory()->author()->create(['name' => 'Nombre Original']);

    Livewire::actingAs($admin)
        ->test('pages::users.index')
        ->call('openEdit', $author->id)
        ->assertSet('name', 'Nombre Original')
        ->assertSet('role', 'author')
        ->set('name', 'Nombre Editado')
        ->set('role', 'admin')
        ->call('save');

    $this->assertDatabaseHas('users', [
        'id' => $author->id,
        'name' => 'Nombre Editado',
        'role' => Role::Admin->value,
    ]);
});

test('editar usuario no requiere password para no cambiar la contrasena', function () {
    $admin = User::factory()->admin()->create();
    $author = User::factory()->author()->create();
    $originalHash = $author->password;

    Livewire::actingAs($admin)
        ->test('pages::users.index')
        ->call('openEdit', $author->id)
        ->set('name', 'Nuevo Nombre')
        ->set('password', null)
        ->call('save');

    expect($author->fresh()->password)->toBe($originalHash);
});

test('editar usuario no permite email duplicado', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->author()->create(['email' => 'existente@example.com']);
    $author = User::factory()->author()->create();

    Livewire::actingAs($admin)
        ->test('pages::users.index')
        ->call('openEdit', $author->id)
        ->set('email', 'existente@example.com')
        ->call('save')
        ->assertHasErrors(['email']);
});

// --- Activar / Desactivar ---

test('un administrador puede desactivar un usuario', function () {
    $admin = User::factory()->admin()->create();
    $author = User::factory()->author()->create(['is_active' => true]);

    Livewire::actingAs($admin)
        ->test('pages::users.index')
        ->call('toggleActive', $author->id);

    expect($author->fresh()->is_active)->toBeFalse();
});

test('un administrador puede activar un usuario inactivo', function () {
    $admin = User::factory()->admin()->create();
    $author = User::factory()->author()->inactive()->create();

    Livewire::actingAs($admin)
        ->test('pages::users.index')
        ->call('toggleActive', $author->id);

    expect($author->fresh()->is_active)->toBeTrue();
});

test('un administrador no puede desactivar su propia cuenta', function () {
    $admin = User::factory()->admin()->create(['is_active' => true]);

    Livewire::actingAs($admin)
        ->test('pages::users.index')
        ->call('toggleActive', $admin->id);

    expect($admin->fresh()->is_active)->toBeTrue();
});

// --- Eliminar ---

test('un administrador puede eliminar un usuario', function () {
    $admin = User::factory()->admin()->create();
    $author = User::factory()->author()->create();

    Livewire::actingAs($admin)
        ->test('pages::users.index')
        ->call('confirmDelete', $author->id)
        ->assertSet('deletingId', $author->id)
        ->call('delete');

    $this->assertDatabaseMissing('users', ['id' => $author->id]);
});

test('un administrador no puede eliminar su propia cuenta', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test('pages::users.index')
        ->call('confirmDelete', $admin->id)
        ->call('delete');

    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});
