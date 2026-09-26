<?php

namespace App\Enums;

/**
 * Separa el stack de programación de los servicios y asistentes que el
 * proyecto consume o con los que se construyó — DeepSeek, Claude Code, Open
 * Library — que no son parte del código pero sí de la historia técnica.
 */
enum TechnologyGroup: string
{
    case Stack = 'stack';
    case Service = 'service';

    public function label(): string
    {
        return match ($this) {
            self::Stack => 'Tecnologías',
            self::Service => 'Servicios',
        };
    }
}
