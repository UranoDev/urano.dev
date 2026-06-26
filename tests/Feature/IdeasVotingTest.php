<?php

use App\Models\Idea;
use App\Models\User;
use App\Models\Vote;
use Livewire\Livewire;

// --- Acceso público ---

test('la pagina publica de ideas es accesible sin autenticacion', function () {
    $this->get(route('ideas.public'))->assertOk();
});

test('la pagina muestra solo ideas aprobadas', function () {
    Idea::factory()->approved()->create(['title' => 'Idea aprobada visible']);
    Idea::factory()->pending()->create(['title' => 'Idea pendiente oculta']);
    Idea::factory()->rejected()->create(['title' => 'Idea rechazada oculta']);

    Livewire::test('pages::ideas.public')
        ->assertSeeText('Idea aprobada visible')
        ->assertDontSeeText('Idea pendiente oculta')
        ->assertDontSeeText('Idea rechazada oculta');
});

// --- Votar ---

test('un usuario autenticado y verificado puede votar por una idea aprobada', function () {
    $user = User::factory()->visitor()->create(['email_verified_at' => now()]);
    $idea = Idea::factory()->approved()->create(['votes_count' => 0]);

    Livewire::actingAs($user)
        ->test('pages::ideas.public')
        ->call('toggleVote', $idea->id);

    expect(Vote::where(['user_id' => $user->id, 'idea_id' => $idea->id])->exists())->toBeTrue()
        ->and($idea->fresh()->votes_count)->toBe(1);
});

test('votar incrementa votes_count en 1', function () {
    $user = User::factory()->visitor()->create(['email_verified_at' => now()]);
    $idea = Idea::factory()->approved()->create(['votes_count' => 5]);

    Livewire::actingAs($user)
        ->test('pages::ideas.public')
        ->call('toggleVote', $idea->id);

    expect($idea->fresh()->votes_count)->toBe(6);
});

// --- Desvotar ---

test('un usuario puede desvotar una idea que ya habia votado', function () {
    $user = User::factory()->visitor()->create(['email_verified_at' => now()]);
    $idea = Idea::factory()->approved()->create(['votes_count' => 1]);
    Vote::factory()->create(['user_id' => $user->id, 'idea_id' => $idea->id]);

    Livewire::actingAs($user)
        ->test('pages::ideas.public')
        ->call('toggleVote', $idea->id);

    expect(Vote::where(['user_id' => $user->id, 'idea_id' => $idea->id])->exists())->toBeFalse()
        ->and($idea->fresh()->votes_count)->toBe(0);
});

test('desvotar decrementa votes_count en 1', function () {
    $user = User::factory()->visitor()->create(['email_verified_at' => now()]);
    $idea = Idea::factory()->approved()->create(['votes_count' => 5]);
    Vote::factory()->create(['user_id' => $user->id, 'idea_id' => $idea->id]);

    Livewire::actingAs($user)
        ->test('pages::ideas.public')
        ->call('toggleVote', $idea->id);

    expect($idea->fresh()->votes_count)->toBe(4);
});

// --- Restricciones ---

test('un usuario no autenticado es redirigido al login al intentar votar', function () {
    $idea = Idea::factory()->approved()->create();

    Livewire::test('pages::ideas.public')
        ->call('toggleVote', $idea->id)
        ->assertRedirect(route('login'));
});

test('un usuario no verificado no puede votar', function () {
    $user = User::factory()->visitor()->create(['email_verified_at' => null]);
    $idea = Idea::factory()->approved()->create();

    Livewire::actingAs($user)
        ->test('pages::ideas.public')
        ->call('toggleVote', $idea->id)
        ->assertForbidden();
});

test('no se puede votar en una idea no aprobada', function () {
    $user = User::factory()->visitor()->create(['email_verified_at' => now()]);
    $idea = Idea::factory()->pending()->create();

    Livewire::actingAs($user)
        ->test('pages::ideas.public')
        ->call('toggleVote', $idea->id)
        ->assertForbidden();
});

// --- Redirect post-login ---

test('toggleVote guarda url.intended en sesion al redirigir al login', function () {
    $idea = Idea::factory()->approved()->create();

    Livewire::test('pages::ideas.public')
        ->call('toggleVote', $idea->id)
        ->assertRedirect(route('login'));

    expect(session('url.intended'))->toBe(route('ideas.public'));
});

// --- votedIdeaIds ---

test('votedIdeaIds contiene los ids de ideas votadas por el usuario', function () {
    $user = User::factory()->visitor()->create(['email_verified_at' => now()]);
    $idea = Idea::factory()->approved()->create();
    Vote::factory()->create(['user_id' => $user->id, 'idea_id' => $idea->id]);

    Livewire::actingAs($user)
        ->test('pages::ideas.public')
        ->assertSet('votedIdeaIds', [$idea->id]);
});

test('votedIdeaIds esta vacio para usuarios no autenticados', function () {
    Livewire::test('pages::ideas.public')
        ->assertSet('votedIdeaIds', []);
});
