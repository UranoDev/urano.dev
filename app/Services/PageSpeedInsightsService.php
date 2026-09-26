<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PageSpeedInsightsService
{
    /**
     * Consulta PageSpeed Insights v5 para una URL y regresa los cuatro
     * puntajes de Lighthouse como enteros 0–100, o `null` si la API falla
     * (tronar aquí dejaría sin puntaje a un proyecto por una cuota agotada
     * o un timeout, en vez de solo no actualizarlo esta vez).
     *
     * @return array{performance: int, accessibility: int, best_practices: int, seo: int}|null
     */
    public function scoresFor(string $url): ?array
    {
        // La API espera `category` repetido (?category=performance&category=seo…),
        // no la notación con corchetes que produciría pasar un array como valor
        // de query a Http::get() — se arma la cadena a mano por eso.
        $query = http_build_query(array_filter([
            'url' => $url,
            'strategy' => 'mobile',
            'key' => config('services.pagespeed.key'),
        ]));
        foreach (['performance', 'accessibility', 'best-practices', 'seo'] as $category) {
            $query .= '&category='.$category;
        }

        $response = Http::timeout(60)->get('https://www.googleapis.com/pagespeedonline/v5/runPagespeed?'.$query);

        if ($response->failed()) {
            Log::warning('PageSpeed Insights: la petición falló', ['url' => $url, 'status' => $response->status()]);

            return null;
        }

        $categories = $response->json('lighthouseResult.categories');

        if (! $categories) {
            Log::warning('PageSpeed Insights: la respuesta no trae categorías', ['url' => $url]);

            return null;
        }

        return [
            'performance' => (int) round(($categories['performance']['score'] ?? 0) * 100),
            'accessibility' => (int) round(($categories['accessibility']['score'] ?? 0) * 100),
            'best_practices' => (int) round(($categories['best-practices']['score'] ?? 0) * 100),
            'seo' => (int) round(($categories['seo']['score'] ?? 0) * 100),
        ];
    }
}
