<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Service;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * El mapa del sitio que lee un buscador. Se sirve desde una ruta y no
     * desde `public/` para que las direcciones salgan del enrutador y una
     * página nueva entre sola.
     */
    public function __invoke(): Response
    {
        $direcciones = collect([
            route('home'),
            route('services.index'),
            route('casos-exito.index'),
            route('blog.index'),
            route('nosotros'),
            route('contact'),
            route('ideas.public'),
            route('links.public'),
        ]);

        $direcciones = $direcciones
            ->merge(Service::query()->orderBy('id')->pluck('slug')
                ->map(fn (string $slug): string => route('services.show', $slug)))
            ->merge(collect(array_keys(config('casos-exito')))
                ->map(fn (string $proyecto): string => route('casos-exito.show', $proyecto)))
            ->merge(Post::query()->where('status', 'published')->orderBy('id')->pluck('slug')
                ->map(fn (string $slug): string => route('blog.show', $slug)));

        return response()
            ->view('sitemap', ['direcciones' => $direcciones])
            ->header('Content-Type', 'application/xml');
    }
}
