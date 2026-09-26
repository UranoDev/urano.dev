<?php

namespace Database\Seeders;

use App\Enums\PortfolioProjectCategory;
use App\Enums\PortfolioProjectStatus;
use App\Enums\TechnologyCategory;
use App\Enums\TechnologyGroup;
use App\Models\PortfolioProject;
use App\Models\PortfolioProjectScreenshot;
use App\Models\PortfolioProjectTechnicalHighlight;
use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class PortfolioProjectSeeder extends Seeder
{
    /**
     * El catálogo de tecnologías, compartido entre proyectos. Vive aquí y no
     * en su propio seeder porque hoy solo lo consume el portafolio.
     *
     * `stack` es el código con el que se construyó; `tool` son los servicios y
     * asistentes que el proyecto usa o con los que se hizo — no se instalan
     * con composer o npm, pero son igual de parte de la historia técnica.
     *
     * @var array<string, array{name: string, group: TechnologyGroup, category: TechnologyCategory, color: string}>
     */
    private array $technologies = [
        'laravel' => ['name' => 'Laravel 13', 'group' => TechnologyGroup::Stack, 'category' => TechnologyCategory::Backend, 'color' => '#FF2D20'],
        'livewire' => ['name' => 'Livewire 4', 'group' => TechnologyGroup::Stack, 'category' => TechnologyCategory::Frontend, 'color' => '#4E56A6'],
        'flux' => ['name' => 'Flux UI', 'group' => TechnologyGroup::Stack, 'category' => TechnologyCategory::Frontend, 'color' => '#7C3AED'],
        'tailwind' => ['name' => 'Tailwind CSS 4', 'group' => TechnologyGroup::Stack, 'category' => TechnologyCategory::Frontend, 'color' => '#06B6D4'],
        'pest' => ['name' => 'Pest', 'group' => TechnologyGroup::Stack, 'category' => TechnologyCategory::Testing, 'color' => '#16A34A'],
        'phpunit' => ['name' => 'PHPUnit', 'group' => TechnologyGroup::Stack, 'category' => TechnologyCategory::Testing, 'color' => '#3C9CD7'],
        'mariadb' => ['name' => 'MariaDB', 'group' => TechnologyGroup::Stack, 'category' => TechnologyCategory::Database, 'color' => '#003545'],
        'plesk' => ['name' => 'Plesk', 'group' => TechnologyGroup::Stack, 'category' => TechnologyCategory::Infra, 'color' => '#1565C0'],
        'deepseek' => ['name' => 'DeepSeek', 'group' => TechnologyGroup::Service, 'category' => TechnologyCategory::Ai, 'color' => '#4D6BFE'],
        'claude' => ['name' => 'Claude Code', 'group' => TechnologyGroup::Service, 'category' => TechnologyCategory::Ai, 'color' => '#CC785C'],
        'open-library' => ['name' => 'Open Library', 'group' => TechnologyGroup::Service, 'category' => TechnologyCategory::Ai, 'color' => '#1B75BB'],
        'google-books' => ['name' => 'Google Books', 'group' => TechnologyGroup::Service, 'category' => TechnologyCategory::Ai, 'color' => '#4285F4'],
    ];

    public function run(): void
    {
        $tech = collect($this->technologies)->map(
            fn (array $data, string $slug) => Technology::updateOrCreate(
                ['slug' => $slug],
                ['name' => $data['name'], 'group' => $data['group'], 'category' => $data['category'], 'color' => $data['color']],
            )
        );

        $this->seedProject($tech, [
            'slug' => 'mi-biblioteca',
            'title' => 'Mi Biblioteca',
            'tagline' => 'Acervo personal con captura por código de barras, Vitrina pública y portadas que nunca faltan',
            'url' => 'https://biblio.urano.dev',
            'status' => PortfolioProjectStatus::Live,
            'category' => PortfolioProjectCategory::Personal,
            'started_year' => 2026,
            'started_month' => 9,
            'ended_year' => null,
            'ended_month' => null,
            'description' => 'Aplicación privada para un acervo de más de 200 libros. Se captura por lector USB en ráfaga, la ficha se arma con fuentes públicas y una IA propone categorías y, cuando hace falta, la descripción. Tiene una Vitrina pública de solo lectura para compartir lo leído, lo que está por comprar y las citas guardadas.',
            'features' => [
                'Captura por lector USB de código de barras, pensada para ráfagas de veinte libros seguidos',
                'Vitrina pública sin sesión: ficha, portada, categorías y libros por comprar',
                'Toda portada existe siempre: si ninguna fuente entrega una buena, se genera una tipográfica con color determinista',
                'Categorías propuestas por IA a partir de la ficha, siempre editables a mano',
                'Descripción escrita por IA solo cuando ninguna fuente la trae, y marcada como tal',
                'Citas guardadas, compartibles como imagen para redes',
                'Un ISBN es único: reescanear uno ya registrado avisa sin duplicar',
            ],
            'cost_label' => '$328 USD',
            'cost_note' => 'No es un precio de cliente — es personal. Es la suma del campo Cost de las 70 issues de su YouTrack (BIB): lo que costó en cómputo de IA cada una de las que ya se resolvieron.',
            'duration_label' => '4 días',
            'duration_note' => 'Del 22 al 25 de septiembre de 2026, de "Inicializar el repositorio" a la versión que corre hoy en producción.',
            'site_structure' => [
                [
                    'title' => 'Vitrina pública',
                    'items' => [
                        ['name' => 'Catálogo', 'description' => 'Libros por comprar y ya leídos, navegable por categoría.'],
                        ['name' => 'Ficha de libro', 'description' => 'Portada, metadatos, cita y notas públicas.'],
                        ['name' => 'Categoría', 'description' => 'Los libros de un mismo tema.'],
                        ['name' => 'Cita', 'description' => 'Página individual de una cita, con imagen para compartir en redes.'],
                        ['name' => 'Privacidad y términos'],
                    ],
                ],
                [
                    'title' => 'El panel (privado)',
                    'items' => [
                        ['name' => 'Estante', 'description' => 'A donde aterriza el login.'],
                        ['name' => 'Dashboard', 'description' => 'Qué trabajo está pendiente.'],
                        ['name' => 'Escaneo', 'description' => 'Captura por lector USB tipo teclado, en ráfaga de veinte libros.'],
                        ['name' => 'Búsqueda por título / Captura manual', 'description' => 'Cuando el ISBN no basta o no existe.'],
                        ['name' => 'Categorías', 'description' => 'Gestión, edición y fusión de las que propone la IA.'],
                        ['name' => 'Buscador de citas'],
                        ['name' => 'Ficha de libro (edición)', 'description' => 'Estado de lectura, notas privadas y procedencia de cada dato.'],
                    ],
                ],
            ],
            'stack' => ['laravel', 'livewire', 'flux', 'tailwind', 'pest', 'mariadb', 'plesk', 'deepseek', 'open-library', 'google-books', 'claude'],
            'highlights' => [
                ['text' => 'Ficha armada con Open Library primero y Google Books de respaldo; ninguna cubre bien las ediciones mexicanas, así que la Captura manual es una vía normal, no un error.', 'tech' => ['open-library', 'google-books']],
                ['text' => 'Agente de IA con salida estructurada, sobre el SDK laravel/ai, para proponer categorías; corre en cola, nunca durante la captura, y reutiliza categorías existentes antes de inventar una nueva.', 'tech' => ['deepseek']],
                ['text' => 'El proveedor y el modelo de IA se eligen desde una pantalla del back, no en el .env — cambiar de DeepSeek a otro proveedor no pide redeploy.', 'tech' => ['deepseek']],
                ['text' => 'Cobertura de más de 1000 pruebas con Pest, corriendo contra base de datos real.', 'tech' => ['pest']],
                ['text' => 'Despliegue en Plesk con versionado CalVer (AAAA.MM.DD.build) en cada push a master.', 'tech' => ['plesk']],
                ['text' => 'Cada Cover y cada Description guardan su procedencia — fuente, IA o propia — y esa procedencia decide si un valor nuevo puede reemplazarlo.', 'tech' => []],
            ],
            'screenshots' => [
                ['path' => 'images/portfolio/mi-biblioteca/hero.png', 'alt' => 'Vitrina pública: catálogo de libros por comprar', 'featured' => true],
                ['path' => 'images/portfolio/mi-biblioteca/acervo.png', 'alt' => 'Acervo con más de 200 libros, portadas reales y generadas', 'featured' => false],
                ['path' => 'images/portfolio/mi-biblioteca/ficha.png', 'alt' => 'Ficha de un libro leído, con metadatos y traducción por IA', 'featured' => false],
            ],
        ]);

        $this->seedProject($tech, [
            'slug' => 'calzaclean',
            'title' => 'CalzaClean',
            'tagline' => 'Sitio con panel propio para un taller de limpieza de tenis en San Juan del Río, Querétaro',
            'url' => 'https://calzaclean.com',
            'status' => PortfolioProjectStatus::Live,
            'category' => PortfolioProjectCategory::Client,
            'started_year' => 2026,
            'started_month' => 9,
            'ended_year' => 2026,
            'ended_month' => 9,
            'description' => 'Sitio público para un taller de limpieza y restauración de tenis a mano. No cobra, no agenda ni cotiza: enseña resultados, dice cuánto cuesta y termina en WhatsApp. Detrás tiene un Panel de cuatro pantallas para que el dueño —única persona que opera el taller— mantenga al día precios, galería y preguntas frecuentes sin tocar código.',
            'features' => [
                'Catálogo de servicios y extras con precio, editable desde el Panel',
                'Galería de resultados antes/después con comparador deslizable',
                'Zonas de recolección a domicilio, cada una con su costo',
                'Preguntas frecuentes editables sin tocar código',
                'Imagen para compartir un trabajo en redes, armada automáticamente por par',
                'Botón directo a WhatsApp: sin cobro, sin agenda ni cotización en línea',
            ],
            'cost_label' => '$8,500 – $15,000 MXN',
            'cost_note' => 'Cubre las cuatro partes del servicio: evaluación de marca, el sitio con su contenido, el plan de acciones para redes y tres meses de seguimiento. Dónde cae depende de cuántas pantallas necesite administrar el negocio y de cuánto contenido haya que escribir desde cero.',
            'duration_label' => 'Una semana',
            'duration_note' => 'Contada desde la firma del contrato. Los tres meses de seguimiento corren después, con el sitio ya publicado.',
            'site_structure' => [
                [
                    'title' => 'Páginas',
                    'items' => [
                        ['name' => 'Portada', 'description' => 'Una sola página con anclas.'],
                        ['name' => 'Precios', 'description' => 'Dirección propia, para mandarla por WhatsApp sin explicar nada — reemplazó la captura de pantalla que antes se reenviaba.'],
                        ['name' => 'Resultados', 'description' => 'La galería completa de pares, que va agregando de a cuatro.'],
                        ['name' => 'Cuidado de tenis', 'description' => 'Guía breve por material.'],
                        ['name' => 'Aviso de privacidad y términos y condiciones'],
                    ],
                ],
                [
                    'title' => 'Secciones de la portada',
                    'items' => [
                        ['name' => 'Servicios y precios', 'description' => 'La lista completa, con los extras agrupados al final.'],
                        ['name' => 'Resultados', 'description' => 'Antes y después de cada par, con una manija que se arrastra para comparar.'],
                        ['name' => 'Cómo funciona', 'description' => 'Cuatro pasos, y ahí mismo la recolección a domicilio con sus zonas y su costo.'],
                        ['name' => 'Materiales', 'description' => 'Qué se le hace a cada uno y con qué.'],
                        ['name' => 'Preguntas frecuentes'],
                        ['name' => 'Contacto', 'description' => 'Horarios, domicilio y redes.'],
                    ],
                ],
                [
                    'title' => 'El panel (backoffice)',
                    'items' => [
                        ['name' => 'Trabajos', 'description' => 'Subir las dos fotos desde el celular, con el par recién terminado enfrente — la pantalla que se usa cada semana.'],
                        ['name' => 'Precios', 'description' => 'Tabla editable, con vista previa de cómo queda publicada.'],
                        ['name' => 'Preguntas y testimonios'],
                        ['name' => 'Ajustes', 'description' => 'Se abre en tres: contacto y redes, datos del negocio con sus zonas de recolección, y un aviso que enciende una franja arriba del sitio.'],
                    ],
                ],
            ],
            'stack' => ['laravel', 'livewire', 'flux', 'tailwind', 'mariadb', 'plesk', 'phpunit', 'claude'],
            'highlights' => [
                ['text' => 'Cada foto se procesa en dos variantes y dos formatos —WebP con respaldo JPEG—, servidas desde un <picture>.', 'tech' => []],
                ['text' => 'Borrar un Trabajo se lleva en cascada sus archivos, miniaturas e imagen para compartir: nada queda huérfano en disco.', 'tech' => []],
                ['text' => 'El orden de la galería y del catálogo se reacomoda dentro de una transacción, para que dos filas nunca peleen por el mismo lugar.', 'tech' => ['mariadb']],
                ['text' => 'Suite de 75 pruebas cubriendo catálogo, galería y panel.', 'tech' => ['phpunit']],
                ['text' => 'Despliegue en Plesk; el dominio corre en su propia raíz httpdocs, distinta a la de otros sitios en el mismo servidor.', 'tech' => ['plesk']],
            ],
            'screenshots' => [
                ['path' => 'images/portfolio/calzaclean/hero.png', 'alt' => 'Portada: limpieza a mano por material, con comparador antes/después', 'featured' => true],
                ['path' => 'images/portfolio/calzaclean/resultados.png', 'alt' => 'Galería de resultados con comparador deslizable antes/después', 'featured' => false],
                ['path' => 'images/portfolio/calzaclean/precios.png', 'alt' => 'Lista de precios por servicio y extras', 'featured' => false],
            ],
        ]);
    }

    /**
     * @param  Collection<string, Technology>  $tech
     * @param  array<string, mixed>  $data
     */
    private function seedProject($tech, array $data): void
    {
        $project = PortfolioProject::updateOrCreate(
            ['slug' => $data['slug']],
            [
                'title' => $data['title'],
                'tagline' => $data['tagline'],
                'url' => $data['url'],
                'status' => $data['status'],
                'category' => $data['category'],
                'started_year' => $data['started_year'],
                'started_month' => $data['started_month'],
                'ended_year' => $data['ended_year'],
                'ended_month' => $data['ended_month'],
                'description' => $data['description'],
                'cost_label' => $data['cost_label'] ?? null,
                'cost_note' => $data['cost_note'] ?? null,
                'duration_label' => $data['duration_label'] ?? null,
                'duration_note' => $data['duration_note'] ?? null,
                'site_structure' => $data['site_structure'] ?? null,
                'features' => $data['features'],
                'sort_order' => 0,
            ],
        );

        $project->technologies()->sync(
            collect($data['stack'])
                ->values()
                ->mapWithKeys(fn (string $slug, int $i) => [$tech[$slug]->id => ['sort_order' => $i]])
                ->all()
        );

        $project->technicalHighlights()->delete();
        foreach ($data['highlights'] as $i => $highlight) {
            PortfolioProjectTechnicalHighlight::create([
                'portfolio_project_id' => $project->id,
                'description' => $highlight['text'],
                'technology_ids' => $highlight['tech']
                    ? collect($highlight['tech'])->map(fn (string $slug) => $tech[$slug]->id)->all()
                    : null,
                'sort_order' => $i,
            ]);
        }

        $project->screenshots()->delete();
        foreach ($data['screenshots'] as $i => $screenshot) {
            PortfolioProjectScreenshot::create([
                'portfolio_project_id' => $project->id,
                'path' => $screenshot['path'],
                'alt' => $screenshot['alt'],
                'is_featured' => $screenshot['featured'],
                'sort_order' => $i,
            ]);
        }
    }
}
