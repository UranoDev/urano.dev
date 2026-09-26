<?php

namespace App\Enums;

enum TechnologyCategory: string
{
    case Backend = 'backend';
    case Frontend = 'frontend';
    case Ai = 'ai';
    case Database = 'database';
    case Infra = 'infra';
    case Testing = 'testing';

    public function label(): string
    {
        return match ($this) {
            self::Backend => 'Backend',
            self::Frontend => 'Frontend',
            self::Ai => 'IA',
            self::Database => 'Base de datos',
            self::Infra => 'Infraestructura',
            self::Testing => 'Testing',
        };
    }
}
