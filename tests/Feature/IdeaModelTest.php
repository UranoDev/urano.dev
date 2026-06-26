<?php

use App\Enums\IdeaStatus;
use App\Models\Idea;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\QueryException;

// --- Idea: estados y factory ---

test('una idea nueva tiene estado pending por defecto', function () {
    $idea = Idea::factory()->create();

    expect($idea->status)->toBe(IdeaStatus::Pending)
        ->and($idea->isPending())->toBeTrue()
        ->and($idea->isApproved())->toBeFalse()
        ->and($idea->isRejected())->toBeFalse();
});

test('se puede crear una idea aprobada', function () {
    $idea = Idea::factory()->approved()->create();

    expect($idea->status)->toBe(IdeaStatus::Approved)
        ->and($idea->isApproved())->toBeTrue();
});

test('se puede crear una idea rechazada', function () {
    $idea = Idea::factory()->rejected()->create();

    expect($idea->status)->toBe(IdeaStatus::Rejected)
        ->and($idea->isRejected())->toBeTrue();
});

// --- Idea: métodos approve/reject ---

test('approve() cambia el estado de una idea a approved', function () {
    $idea = Idea::factory()->pending()->create();

    $idea->approve();

    expect($idea->fresh()->status)->toBe(IdeaStatus::Approved);
});

test('reject() cambia el estado de una idea a rejected', function () {
    $idea = Idea::factory()->pending()->create();

    $idea->reject();

    expect($idea->fresh()->status)->toBe(IdeaStatus::Rejected);
});

// --- Relaciones ---

test('una idea pertenece a un usuario', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->create(['user_id' => $user->id]);

    expect($idea->user->id)->toBe($user->id);
});

test('un usuario tiene muchas ideas', function () {
    $user = User::factory()->create();
    Idea::factory()->count(3)->create(['user_id' => $user->id]);

    expect($user->ideas)->toHaveCount(3);
});

test('una idea tiene muchos votos', function () {
    $idea = Idea::factory()->approved()->create();
    Vote::factory()->count(3)->create(['idea_id' => $idea->id]);

    expect($idea->votes)->toHaveCount(3);
});

test('un usuario tiene muchos votos', function () {
    $user = User::factory()->create();
    $ideas = Idea::factory()->approved()->count(2)->create();

    Vote::factory()->create(['user_id' => $user->id, 'idea_id' => $ideas[0]->id]);
    Vote::factory()->create(['user_id' => $user->id, 'idea_id' => $ideas[1]->id]);

    expect($user->votes)->toHaveCount(2);
});

// --- Vote: unicidad ---

test('no se puede votar dos veces por la misma idea', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->approved()->create();

    Vote::factory()->create(['user_id' => $user->id, 'idea_id' => $idea->id]);

    expect(fn () => Vote::factory()->create(['user_id' => $user->id, 'idea_id' => $idea->id]))
        ->toThrow(QueryException::class);
});

// --- votes_count ---

test('una idea nueva tiene votes_count en cero', function () {
    $idea = Idea::factory()->create();

    expect($idea->votes_count)->toBe(0);
});
