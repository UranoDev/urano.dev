<?php

namespace App\Models;

use App\Enums\ProjectTimeframe;
use Database\Factories\ProjectInquiryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'company', 'project_description', 'timeframe'])]
class ProjectInquiry extends Model
{
    /** @use HasFactory<ProjectInquiryFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'timeframe' => ProjectTimeframe::class,
        ];
    }
}
