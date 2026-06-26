<?php

namespace App\Observers;

use App\Models\Post;
use App\Services\PostStaticGenerator;
use Illuminate\Support\Facades\File;

class PostObserver
{
    public function __construct(protected PostStaticGenerator $generator) {}

    /**
     * Handle the Post "saving" event.
     */
    public function saving(Post $post): void
    {
        if ($post->isDirty('status') && $post->status === 'published' && ! $post->published_at) {
            $post->published_at = now();
        }
    }

    /**
     * Handle the Post "saved" event.
     */
    public function saved(Post $post): void
    {
        if ($post->status === 'published') {
            // Si el slug cambió, borrar el archivo anterior antes de generar el nuevo
            if ($post->isDirty('slug') && $post->getOriginal('slug')) {
                $oldFileName = $post->getOriginal('slug').'.html';
                $oldFilePath = storage_path('app/public/posts/'.$oldFileName);
                if (File::exists($oldFilePath)) {
                    File::delete($oldFilePath);
                }
            }

            // Evitar bucle infinito al actualizar el static_path
            $post->withoutEvents(function () use ($post) {
                $post->load('tags', 'author');                $staticPath = $this->generator->generate($post);
                $post->static_path = $staticPath;
                $post->save();
            });
        } elseif ($post->isDirty('status') && $post->getOriginal('status') === 'published') {
            // Si deja de estar publicado, opcionalmente podríamos borrar el HTML
            // pero por ahora lo dejamos o lo borramos si es archived/draft
            $this->generator->delete($post);
            $post->withoutEvents(function () use ($post) {
                $post->update(['static_path' => null]);
            });
        }
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        $this->generator->delete($post);
    }
}
