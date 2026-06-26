<?php

use App\Enums\Role;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

test('el boton de Google redirige al proveedor', function () {
    Socialite::fake('google');

    $response = $this->get(route('auth.google.redirect'));

    $response->assertRedirect();
});

test('un usuario nuevo se crea via Google OAuth con rol visitor y email verificado', function () {
    Socialite::fake('google', (new SocialiteUser)->map([
        'id' => 'google-123',
        'name' => 'Juan Perez',
        'email' => 'juan@example.com',
    ]));

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('users', [
        'email' => 'juan@example.com',
        'google_id' => 'google-123',
        'role' => Role::Visitor->value,
    ]);

    $user = User::where('email', 'juan@example.com')->first();
    expect($user->email_verified_at)->not->toBeNull()
        ->and($user->isVisitor())->toBeTrue();

    $this->assertAuthenticatedAs($user);
});

test('un usuario existente con el mismo email vincula su google_id al hacer login con Google', function () {
    $existingUser = User::factory()->create([
        'email' => 'existente@example.com',
        'google_id' => null,
    ]);

    Socialite::fake('google', (new SocialiteUser)->map([
        'id' => 'google-456',
        'name' => $existingUser->name,
        'email' => 'existente@example.com',
    ]));

    $this->get(route('auth.google.callback'));

    expect($existingUser->fresh()->google_id)->toBe('google-456');
    $this->assertAuthenticatedAs($existingUser->fresh());
});

test('un usuario existente con google_id puede volver a iniciar sesion', function () {
    $user = User::factory()->create([
        'google_id' => 'google-789',
        'email' => 'vuelve@example.com',
    ]);

    Socialite::fake('google', (new SocialiteUser)->map([
        'id' => 'google-789',
        'name' => $user->name,
        'email' => 'vuelve@example.com',
    ]));

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('el usuario de Google queda verificado aunque registre email sin verificar previamente', function () {
    $unverifiedUser = User::factory()->unverified()->create([
        'email' => 'sin-verificar@example.com',
        'google_id' => null,
    ]);

    Socialite::fake('google', (new SocialiteUser)->map([
        'id' => 'google-unverified',
        'name' => $unverifiedUser->name,
        'email' => 'sin-verificar@example.com',
    ]));

    $this->get(route('auth.google.callback'));

    expect($unverifiedUser->fresh()->email_verified_at)->not->toBeNull();
});
