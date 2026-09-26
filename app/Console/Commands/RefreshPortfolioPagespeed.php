<?php

namespace App\Console\Commands;

use App\Models\PortfolioProject;
use App\Services\PageSpeedInsightsService;
use Illuminate\Console\Command;

class RefreshPortfolioPagespeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portfolio:refresh-pagespeed {slug? : Solo este proyecto; sin argumento, todos los que tengan url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mide el puntaje de PageSpeed Insights de los proyectos del portafolio y lo guarda.';

    public function handle(PageSpeedInsightsService $pagespeed): int
    {
        $projects = PortfolioProject::whereNotNull('url')
            ->when($this->argument('slug'), fn ($query, $slug) => $query->where('slug', $slug))
            ->get();

        if ($projects->isEmpty()) {
            $this->error('No hay proyectos con url que medir.');

            return Command::FAILURE;
        }

        foreach ($projects as $index => $project) {
            $this->info("Midiendo {$project->title} ({$project->url})…");

            $scores = $pagespeed->scoresFor($project->url);

            if (! $scores) {
                $this->warn("  No se pudo medir {$project->title}; se deja el último puntaje guardado.");

                continue;
            }

            $project->update([
                'pagespeed_performance' => $scores['performance'],
                'pagespeed_accessibility' => $scores['accessibility'],
                'pagespeed_best_practices' => $scores['best_practices'],
                'pagespeed_seo' => $scores['seo'],
                'pagespeed_measured_at' => now(),
            ]);

            $this->info("  Rendimiento {$scores['performance']}, Accesibilidad {$scores['accessibility']}, Buenas prácticas {$scores['best_practices']}, SEO {$scores['seo']}.");

            // Para no pegarle a la cuota de golpe cuando se miden varios seguidos.
            if ($index < $projects->count() - 1) {
                sleep(1);
            }
        }

        return Command::SUCCESS;
    }
}
