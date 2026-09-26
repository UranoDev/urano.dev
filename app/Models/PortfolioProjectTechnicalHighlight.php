<?php

namespace App\Models;

use Database\Factories\PortfolioProjectTechnicalHighlightFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['portfolio_project_id', 'description', 'technology_ids', 'sort_order'])]
class PortfolioProjectTechnicalHighlight extends Model
{
    /** @use HasFactory<PortfolioProjectTechnicalHighlightFactory> */
    use HasFactory;

    protected $table = 'portfolio_highlights';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'technology_ids' => 'array',
        ];
    }

    public function portfolioProject(): BelongsTo
    {
        return $this->belongsTo(PortfolioProject::class);
    }

    /**
     * @return Collection<int, Technology>
     */
    public function relatedTechnologies(): Collection
    {
        return Technology::whereIn('id', $this->technology_ids ?? [])->get();
    }
}
