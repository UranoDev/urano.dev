<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\PostStaticGenerator;
use Illuminate\Console\Command;

class RegenerateStaticPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:regenerate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vuelve a generar el HTML estático de los artículos publicados con el layout y los datos actuales.';

    /**
     * El HTML de cada artículo se escribe al guardarlo, así que un cambio en el
     * layout o en la biografía del autor no llega a los ya publicados hasta
     * que se regeneran. deploy.sh lo corre en cada despliegue.
     */
    public function handle(PostStaticGenerator $generator): int
    {
        $posts = Post::with('tags', 'author')->where('status', 'published')->get();

        foreach ($posts as $post) {
            $post->static_path = $generator->generate($post);
            $post->saveQuietly();
        }

        $this->info($posts->count() === 1
            ? '1 artículo regenerado.'
            : "{$posts->count()} artículos regenerados.");

        return Command::SUCCESS;
    }
}
