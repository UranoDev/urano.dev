<?php

use App\Enums\Role;
use App\Models\User;

test('un usuario nuevo tiene el rol de visitante por defecto', function () {
    $user = User::factory()->create();

    expect($user->role)->toBe(Role::Visitor);
});

test('un usuario admin tiene el rol correcto', function () {
    $user = User::factory()->admin()->create();

    expect($user->role)->toBe(Role::Admin)
        ->and($user->isAdmin())->toBeTrue()
        ->and($user->isAuthor())->toBeFalse()
        ->and($user->isVisitor())->toBeFalse();
});

test('un usuario author tiene el rol correcto', function () {
    $user = User::factory()->author()->create();

    expect($user->role)->toBe(Role::Author)
        ->and($user->isAuthor())->toBeTrue()
        ->and($user->isAdmin())->toBeFalse()
        ->and($user->isVisitor())->toBeFalse();
});

test('un usuario visitor tiene el rol correcto', function () {
    $user = User::factory()->visitor()->create();

    expect($user->role)->toBe(Role::Visitor)
        ->and($user->isVisitor())->toBeTrue()
        ->and($user->isAdmin())->toBeFalse()
        ->and($user->isAuthor())->toBeFalse();
});

test('el rol se persiste correctamente en la base de datos', function () {
    $user = User::factory()->admin()->create();

    $fresh = User::find($user->id);

    expect($fresh->role)->toBe(Role::Admin)
        ->and($fresh->isAdmin())->toBeTrue();
});

test('el rol se puede cambiar', function () {
    $user = User::factory()->visitor()->create();

    $user->update(['role' => Role::Author]);

    expect($user->fresh()->role)->toBe(Role::Author)
        ->and($user->fresh()->isAuthor())->toBeTrue();
});
