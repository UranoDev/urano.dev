<?php

namespace App\Enums;

enum PortfolioProjectCategory: string
{
    case Saas = 'saas';
    case Tool = 'tool';
    case Client = 'client';
    case Personal = 'personal';

    public function label(): string
    {
        return match ($this) {
            self::Saas => 'SaaS',
            self::Tool => 'Herramienta',
            self::Client => 'Cliente',
            self::Personal => 'Personal',
        };
    }
}
