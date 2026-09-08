<?php

namespace App\Enums;

enum ProjectTimeframe: string
{
    case AsSoonAsPossible = 'lo-antes-posible';
    case NextThreeMonths = 'proximos-3-meses';
    case ThisYear = 'este-ano';
    case Exploring = 'explorando';

    public function label(): string
    {
        return match ($this) {
            self::AsSoonAsPossible => 'Lo antes posible',
            self::NextThreeMonths => 'En los próximos 3 meses',
            self::ThisYear => 'Este año',
            self::Exploring => 'Todavía estoy explorando',
        };
    }
}
