<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /** @use HasFactory */
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'category',
        'meta_title',
        'hero_title',
        'hero_desc',
        'cta_text',
        'benefits_title',
        'benefits_subtitle',
        'benefits',
        'quote',
        'quote_author',
        'modules_title',
        'modules',
        'cta_title',
        'cta_desc',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'benefits' => 'array',
            'modules' => 'array',
        ];
    }
}
