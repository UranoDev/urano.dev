<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\File;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class PostStaticGenerator
{
    protected string $storagePath;

    public function __construct()
    {
        $this->storagePath = storage_path('app/public/posts');

        if (! File::exists($this->storagePath)) {
            File::makeDirectory($this->storagePath, 0755, true);
        }
    }

    /**
     * Genera el archivo HTML estático para un post.
     */
    public function generate(Post $post): string
    {
        $converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $htmlContent = $converter->convert($post->content)->getContent();

        // Estructura básica de HTML para el post
        $fullHtml = $this->wrapInLayout($post, $htmlContent);

        $fileName = ($post->slug ?: 'post-'.$post->id).'.html';
        $filePath = $this->storagePath.DIRECTORY_SEPARATOR.$fileName;

        File::put($filePath, $fullHtml);

        return 'posts/'.$fileName;
    }

    /**
     * Elimina el archivo HTML estático de un post.
     */
    public function delete(Post $post): void
    {
        if ($post->static_path) {
            $filePath = storage_path('app/public/'.$post->static_path);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }
    }

    /**
     * Envuelve el contenido en un layout que contiene el look del home y del sitio.
     */
    protected function wrapInLayout(Post $post, string $content): string
    {
        return view('blog.post-static', [
            'post' => $post,
            'content' => $content,
        ])->render();    }
}
