<?php

namespace App\Models;

use Database\Factories\PortfolioProjectScreenshotFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['portfolio_project_id', 'path', 'alt', 'is_featured', 'sort_order'])]
class PortfolioProjectScreenshot extends Model
{
    /** @use HasFactory<PortfolioProjectScreenshotFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
        ];
    }

    public function portfolioProject(): BelongsTo
    {
        return $this->belongsTo(PortfolioProject::class);
    }

    public function url(): string
    {
        return asset($this->path);
    }
}
