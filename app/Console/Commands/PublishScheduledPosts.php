<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Busca posts con estado "scheduled" cuya fecha de publicación haya pasado y los publica.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $posts = Post::where('status', 'scheduled')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get();

        if ($posts->isEmpty()) {
            $this->info('No hay posts programados para publicar.');

            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($posts as $post) {
            $post->status = 'published';
            $post->save();
            $count++;
            $this->info("Post publicado: {$post->title} ({$post->slug})");
        }

        $this->info("Se publicaron {$count} posts programados con éxito.");

        return Command::SUCCESS;
    }
}
