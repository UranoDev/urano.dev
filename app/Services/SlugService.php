<?php

namespace App\Services;

use Illuminate\Support\Str;

class SlugService
{
    /**
     * Genera un slug único para un modelo dado.
     *
     * @param  string  $title  El título o string base.
     * @param  string  $modelClass  FQCN del modelo.
     * @param  int|null  $ignoreId  ID a ignorar (útil en actualizaciones).
     */
    public function generateUniqueSlug(string $title, string $modelClass, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $modelClass, $ignoreId) || $this->isReservedRoute($slug)) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Verifica si el slug ya existe en la base de datos para el modelo.
     */
    protected function slugExists(string $slug, string $modelClass, ?int $ignoreId): bool
    {
        $query = $modelClass::where('slug', $slug);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }

    /**
     * Verifica si el slug colisiona con una ruta reservada del sistema.
     */
    protected function isReservedRoute(string $slug): bool
    {
        $reserved = [
            'dashboard',
            'login',
            'register',
            'logout',
            'about',
            'nosotros',
            'pricing',
            'links',
            'ideas',
            'blog',
            'servicios',            'auth',
            'settings',
            'profile',
        ];

        return in_array(strtolower($slug), $reserved);
    }
}
