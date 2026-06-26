<?php

namespace App\Models;

use App\Enums\IdeaStatus;
use Database\Factories\IdeaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'title', 'body', 'status', 'votes_count'])]
class Idea extends Model
{
    /** @use HasFactory<IdeaFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => IdeaStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function isPending(): bool
    {
        return $this->status === IdeaStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === IdeaStatus::Approved;
    }

    public function isRejected(): bool
    {
        return $this->status === IdeaStatus::Rejected;
    }

    public function approve(): void
    {
        $this->update(['status' => IdeaStatus::Approved]);
    }

    public function reject(): void
    {
        $this->update(['status' => IdeaStatus::Rejected]);
    }

    public function hasVotedBy(User $user): bool
    {
        return $this->votes()->where('user_id', $user->id)->exists();
    }
}
