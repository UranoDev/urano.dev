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
        $slug = $this->argument('slug');

        $projects = PortfolioProject::whereNotNull('url')
            ->when($slug, fn ($query) => $query->where('slug', $slug))
            ->get();

        if ($projects->isEmpty()) {
            $this->error('No hay proyectos con url que medir.');

            return Command::FAILURE;
        }

        // Un slug explícito es una orden directa de quien lo corre a mano: se
        // mide sin importar cuándo se midió la última vez. Sin slug —como lo
        // llama el scheduler todos los días— no vale la pena gastar cuota en un
        // proyecto que ya se midió esta semana.
        $forzar = (bool) $slug;
        $porMedir = $projects->filter(fn (PortfolioProject $project) => $forzar || $this->desactualizado($project))->values();

        foreach ($projects->diff($porMedir) as $reciente) {
            $this->info("  {$reciente->title}: medido hace menos de 7 días, se deja como está.");
        }

        if ($porMedir->isEmpty()) {
            $this->info('Nada que medir: todo se refrescó hace menos de 7 días.');

            return Command::SUCCESS;
        }

        foreach ($porMedir as $index => $project) {
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
            if ($index < $porMedir->count() - 1) {
                sleep(1);
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Si nunca se midió, o si la última medición tiene más de 7 días.
     */
    private function desactualizado(PortfolioProject $project): bool
    {
        return $project->pagespeed_measured_at === null
            || $project->pagespeed_measured_at->lt(now()->subDays(7));
    }
}
