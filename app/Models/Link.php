<?php

namespace App\Models;

use App\Enums\LinkType;
use Database\Factories\LinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'url', 'type', 'post_id', 'sort_order', 'is_active', 'user_id'])]class Link extends Model
{
    /** @use HasFactory<LinkFactory> */
    use HasFactory;

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => LinkType::class,
            'is_active' => 'boolean',
        ];
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(LinkClick::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function getResolvedUrl(): ?string
    {
        if ($this->isInternal() && $this->post_id) {
            $post = $this->post;
            if ($post && $post->static_path) {
                return asset('storage/'.$post->static_path);
            }
        }

        return $this->url;
    }
    public function isExternal(): bool
    {
        return $this->type === LinkType::External;
    }

    public function isInternal(): bool
    {
        return $this->type === LinkType::Internal;
    }
}
