<?php

use App\Enums\IdeaStatus;
use App\Models\Idea;
use App\Models\User;
use Livewire\Livewire;

// --- Orden por votos ---

test('las ideas aprobadas se ordenan por votos de forma descendente', function () {
    Idea::factory()->approved()->create(['title' => 'Pocos votos', 'votes_count' => 1]);
    Idea::factory()->approved()->create(['title' => 'Muchos votos', 'votes_count' => 10]);
    Idea::factory()->approved()->create(['title' => 'Votos medios', 'votes_count' => 5]);

    Livewire::test('pages::ideas.public')
        ->assertSeeInOrder(['Muchos votos', 'Votos medios', 'Pocos votos']);
});

// --- Sugerir idea ---

test('un usuario verificado puede sugerir una idea', function () {
    $user = User::factory()->visitor()->create(['email_verified_at' => now()]);

    Livewire::actingAs($user)
        ->test('pages::ideas.public')
        ->call('openSuggestionForm')
        ->assertSet('showSuggestionForm', true)
        ->set('suggestionTitle', 'Mi nueva idea')
        ->set('suggestionBody', 'Descripcion de la idea sugerida')
        ->call('suggestIdea');

    $this->assertDatabaseHas('ideas', [
        'title' => 'Mi nueva idea',
        'body' => 'Descripcion de la idea sugerida',
        'user_id' => $user->id,
        'status' => IdeaStatus::Pending->value,
    ]);
});

test('sugerir idea resetea el formulario', function () {
    $user = User::factory()->visitor()->create(['email_verified_at' => now()]);

    Livewire::actingAs($user)
        ->test('pages::ideas.public')
        ->call('openSuggestionForm')
        ->set('suggestionTitle', 'Titulo')
        ->set('suggestionBody', 'Cuerpo')
        ->call('suggestIdea')
        ->assertSet('suggestionTitle', '')
        ->assertSet('suggestionBody', '')
        ->assertSet('showSuggestionForm', false);
});

test('sugerir idea requiere titulo y cuerpo', function () {
    $user = User::factory()->visitor()->create(['email_verified_at' => now()]);

    Livewire::actingAs($user)
        ->test('pages::ideas.public')
        ->call('openSuggestionForm')
        ->set('suggestionTitle', '')
        ->set('suggestionBody', '')
        ->call('suggestIdea')
        ->assertHasErrors(['suggestionTitle', 'suggestionBody']);
});

test('un usuario no autenticado es redirigido al login al sugerir idea', function () {
    Livewire::test('pages::ideas.public')
        ->set('suggestionTitle', 'Titulo')
        ->set('suggestionBody', 'Cuerpo')
        ->call('suggestIdea')
        ->assertRedirect(route('login'));

    expect(session('url.intended'))->toBe(route('ideas.public'));
});

test('un usuario no verificado no puede sugerir idea', function () {
    $user = User::factory()->visitor()->create(['email_verified_at' => null]);

    Livewire::actingAs($user)
        ->test('pages::ideas.public')
        ->set('suggestionTitle', 'Titulo')
        ->set('suggestionBody', 'Cuerpo')
        ->call('suggestIdea')
        ->assertForbidden();
});

// --- Ideas del usuario (pendientes / rechazadas) ---

test('un usuario ve sus propias ideas pendientes en la pagina publica', function () {
    $user = User::factory()->visitor()->create(['email_verified_at' => now()]);
    Idea::factory()->pending()->create(['user_id' => $user->id, 'title' => 'Mi idea pendiente']);

    Livewire::actingAs($user)
        ->test('pages::ideas.public')
        ->assertSeeText('Mi idea pendiente')
        ->assertSeeText('Pendiente');
});

test('un usuario ve sus propias ideas rechazadas en la pagina publica', function () {
    $user = User::factory()->visitor()->create(['email_verified_at' => now()]);
    Idea::factory()->rejected()->create(['user_id' => $user->id, 'title' => 'Mi idea rechazada']);

    Livewire::actingAs($user)
        ->test('pages::ideas.public')
        ->assertSeeText('Mi idea rechazada')
        ->assertSeeText('Rechazada');
});

test('un usuario no ve ideas pendientes de otros usuarios', function () {
    $user = User::factory()->visitor()->create(['email_verified_at' => now()]);
    $otherUser = User::factory()->visitor()->create(['email_verified_at' => now()]);
    Idea::factory()->pending()->create(['user_id' => $otherUser->id, 'title' => 'Idea ajena pendiente']);

    Livewire::actingAs($user)
        ->test('pages::ideas.public')
        ->assertDontSeeText('Idea ajena pendiente');
});

test('un usuario no ve ideas rechazadas de otros usuarios', function () {
    $user = User::factory()->visitor()->create(['email_verified_at' => now()]);
    $otherUser = User::factory()->visitor()->create(['email_verified_at' => now()]);
    Idea::factory()->rejected()->create(['user_id' => $otherUser->id, 'title' => 'Idea ajena rechazada']);

    Livewire::actingAs($user)
        ->test('pages::ideas.public')
        ->assertDontSeeText('Idea ajena rechazada');
});

// --- Cancelar formulario ---

test('cancelar el formulario de sugerencia lo cierra y resetea', function () {
    $user = User::factory()->visitor()->create(['email_verified_at' => now()]);

    Livewire::actingAs($user)
        ->test('pages::ideas.public')
        ->call('openSuggestionForm')
        ->set('suggestionTitle', 'Borrador')
        ->set('suggestionBody', 'Texto borrador')
        ->call('closeSuggestionForm')
        ->assertSet('showSuggestionForm', false)
        ->assertSet('suggestionTitle', '')
        ->assertSet('suggestionBody', '');
});

// --- Redirect post-login ---

test('el link de login incluye el parametro intended', function () {
    Livewire::test('pages::ideas.public')
        ->assertSeeHtml(route('login', ['intended' => route('ideas.public')]));
});

test('la pagina de login almacena el intended en la sesion', function () {
    $this->get(route('login', ['intended' => route('ideas.public')]));

    expect(session('url.intended'))->toBe(route('ideas.public'));
});

test('la pagina de login ignora intended con URL externa', function () {
    $this->get(route('login', ['intended' => 'https://evil.com/phish']));

    expect(session()->has('url.intended'))->toBeFalse();
});

// --- Visibilidad del boton de sugerir ---

test('un usuario verificado ve el boton de sugerir idea', function () {
    $user = User::factory()->visitor()->create(['email_verified_at' => now()]);

    Livewire::actingAs($user)
        ->test('pages::ideas.public')
        ->assertSeeText('Sugerir una idea');
});

test('un usuario no verificado no ve el boton de sugerir idea', function () {
    $user = User::factory()->visitor()->create(['email_verified_at' => null]);

    Livewire::actingAs($user)
        ->test('pages::ideas.public')
        ->assertDontSeeText('Sugerir una idea');
});

test('un visitante no autenticado no ve el boton de sugerir idea', function () {
    Livewire::test('pages::ideas.public')
        ->assertDontSeeText('Sugerir una idea');
});
