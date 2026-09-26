<?php

namespace App\Enums;

enum PortfolioProjectStatus: string
{
    case Live = 'live';
    case Beta = 'beta';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Live => 'En producción',
            self::Beta => 'Beta',
            self::Archived => 'Archivado',
        };
    }
}
