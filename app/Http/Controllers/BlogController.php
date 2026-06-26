<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\File;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::with('tags')
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return view('blog.index', compact('posts'));
    }
    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        if (! $post->static_path) {
            abort(404, 'El post no tiene un archivo estático generado.');
        }

        $path = storage_path('app/public/'.$post->static_path);

        if (! File::exists($path)) {
            abort(404, 'El archivo estático del post no existe.');
        }

        return response(File::get($path), 200, [
            'Content-Type' => 'text/html',
        ]);
    }
}
