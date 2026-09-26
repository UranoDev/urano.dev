<?php

namespace App\Models;

use App\Enums\PortfolioProjectCategory;
use App\Enums\PortfolioProjectStatus;
use Database\Factories\PortfolioProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'slug',
    'title',
    'tagline',
    'url',
    'logo_path',
    'favicon_path',
    'status',
    'category',
    'started_year',
    'started_month',
    'ended_year',
    'ended_month',
    'description',
    'cost_label',
    'cost_note',
    'duration_label',
    'duration_note',
    'site_structure',
    'features',
    'sort_order',
])]
class PortfolioProject extends Model
{
    /** @use HasFactory<PortfolioProjectFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PortfolioProjectStatus::class,
            'category' => PortfolioProjectCategory::class,
            'features' => 'array',
            'site_structure' => 'array',
        ];
    }

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class)
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function screenshots(): HasMany
    {
        return $this->hasMany(PortfolioProjectScreenshot::class)->orderBy('sort_order');
    }

    public function technicalHighlights(): HasMany
    {
        return $this->hasMany(PortfolioProjectTechnicalHighlight::class)->orderBy('sort_order');
    }

    public function featuredScreenshot(): ?PortfolioProjectScreenshot
    {
        return $this->screenshots->firstWhere('is_featured', true) ?? $this->screenshots->first();
    }

    /**
     * "Septiembre 2026", o "Septiembre 2026 – Enero 2027" cuando el cierre cae
     * en otro mes. Sin started_month, se queda solo en el año.
     */
    public function periodLabel(): string
    {
        $start = $this->started_month
            ? self::MONTHS[$this->started_month].' '.$this->started_year
            : (string) $this->started_year;

        if (! $this->ended_year) {
            return $start;
        }

        $sameMonth = $this->ended_month === $this->started_month && $this->ended_year === $this->started_year;

        if ($sameMonth) {
            return $start;
        }

        $end = $this->ended_month
            ? self::MONTHS[$this->ended_month].' '.$this->ended_year
            : (string) $this->ended_year;

        return "{$start} – {$end}";
    }

    private const MONTHS = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];
}
