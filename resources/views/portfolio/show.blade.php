<x-layouts.app>
    <x-slot:title>
        {{ $project->title }} | Portafolio | Urano Dev
    </x-slot:title>
    {{-- El lema es corto para compartir; la descripción da el contexto completo. --}}
    <x-slot:description>{{ \Illuminate\Support\Str::limit($project->description ?: $project->tagline, 200) }}</x-slot:description>
    @if ($project->featuredScreenshot())
        <x-slot:image>{{ $project->featuredScreenshot()->url() }}</x-slot:image>
    @endif

    <section class="max-w-6xl mx-auto px-fluid-sm pt-fluid-lg pb-8">
        <x-portfolio.project-card :project="$project" :linkable="false" />
    </section>

    {{-- Descripción y características --}}
    <section class="max-w-6xl mx-auto px-fluid-sm py-fluid-md border-t border-frost-border">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Descripción</span>
                <p class="text-base text-frost-muted leading-relaxed mt-4 max-w-prose">{{ $project->description }}</p>
            </div>

            @if ($project->features)
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Características</span>
                    <ul class="space-y-3 mt-4">
                        @foreach ($project->features as $feature)
                            <li class="flex gap-2.5 text-sm leading-relaxed">
                                <span class="text-frost-muted mt-0.5 shrink-0">✓</span>
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </section>

    {{-- Funcionalidades técnicas --}}
    @if ($project->technicalHighlights->isNotEmpty())
        <section class="max-w-6xl mx-auto px-fluid-sm py-fluid-md border-t border-frost-border">
            <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Por dentro</span>
            <h2 class="text-3xl md:text-4xl font-bold tracking-tight mt-2 mb-6">Funcionalidades técnicas</h2>

            <ul class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($project->technicalHighlights as $highlight)
                    <li class="border border-frost-border p-6 bg-white text-sm text-frost-muted leading-relaxed">
                        {{ $highlight->description }}
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- Páginas y secciones --}}
    @if ($project->site_structure)
        <section class="max-w-6xl mx-auto px-fluid-sm py-fluid-md border-t border-frost-border">
            <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Qué se construyó</span>
            <h2 class="text-3xl md:text-4xl font-bold tracking-tight mt-2 mb-6">Las páginas y secciones</h2>

            {{-- Tailwind no puede ver una clase armada en runtime, así que el
                 conteo de columnas se resuelve aquí, no interpolando el número
                 dentro de la clase (esa clase nunca compilaría). --}}
            <div @class([
                'grid grid-cols-1 gap-6',
                'md:grid-cols-2' => count($project->site_structure) === 2,
                'md:grid-cols-3' => count($project->site_structure) >= 3,
            ])>
                @foreach ($project->site_structure as $group)
                    <div class="border border-frost-border p-6 bg-white">
                        <h3 class="text-lg font-semibold tracking-tight mb-4">{{ $group['title'] }}</h3>
                        <ul class="space-y-3 text-sm text-frost-muted leading-relaxed">
                            @foreach ($group['items'] as $item)
                                <li>
                                    <span class="text-frost-dark font-medium">{{ $item['name'] }}</span>
                                    @if (! empty($item['description']))
                                        — {{ $item['description'] }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- PageSpeed Insights --}}
    @if ($project->pagespeed_measured_at)
        <section class="max-w-6xl mx-auto px-fluid-sm py-fluid-md border-t border-frost-border">
            <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Qué tan rápido carga</span>
            <h2 class="text-3xl md:text-4xl font-bold tracking-tight mt-2 mb-6">PageSpeed Insights</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ([
                    'Rendimiento' => $project->pagespeed_performance,
                    'Accesibilidad' => $project->pagespeed_accessibility,
                    'Buenas prácticas' => $project->pagespeed_best_practices,
                    'SEO' => $project->pagespeed_seo,
                ] as $label => $score)
                    <div class="border border-frost-border p-6 bg-white text-center">
                        <p @class([
                            'text-3xl font-bold tracking-tight',
                            'text-green-700' => $score >= 90,
                            'text-amber-700' => $score >= 50 && $score < 90,
                            'text-red-700' => $score < 50,
                        ])>{{ $score }}</p>
                        <p class="text-xs text-frost-muted uppercase tracking-widest mt-2">{{ $label }}</p>
                    </div>
                @endforeach
            </div>

            <p class="text-sm text-frost-muted mt-6">
                Medido el {{ $project->pagespeedMeasuredAtLabel() }} —
                <a href="https://pagespeed.web.dev/analysis?url={{ urlencode($project->url) }}&form_factor=mobile" target="_blank" rel="noopener" class="underline hover:text-frost-dark transition">verificarlo en tiempo real ↗</a>
            </p>
        </section>
    @endif

    {{-- Cuánto cuesta y cuánto tarda --}}
    @if ($project->cost_label || $project->duration_label)
        <section class="max-w-6xl mx-auto px-fluid-sm py-fluid-md border-t border-frost-border">
            <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Cuánto cuesta y cuánto tarda</span>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                @if ($project->cost_label)
                    <div class="border border-frost-border p-6 bg-white">
                        <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">
                            {{ $project->category === \App\Enums\PortfolioProjectCategory::Personal ? 'Costo estimado' : 'Costo' }}
                        </span>
                        <p class="text-2xl font-bold tracking-tight mt-2">{{ $project->cost_label }}</p>
                        @if ($project->cost_note)
                            <p class="text-sm text-frost-muted leading-relaxed mt-3">{{ $project->cost_note }}</p>
                        @endif
                    </div>
                @endif
                @if ($project->duration_label)
                    <div class="border border-frost-border p-6 bg-white">
                        <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Tiempo</span>
                        <p class="text-2xl font-bold tracking-tight mt-2">{{ $project->duration_label }}</p>
                        @if ($project->duration_note)
                            <p class="text-sm text-frost-muted leading-relaxed mt-3">{{ $project->duration_note }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </section>
    @endif

    <section class="max-w-6xl mx-auto px-fluid-sm py-8">
        <x-frost.cta
            title="¿Quieres algo parecido para tu negocio?"
            buttonText="Cuéntanos qué necesitas"
            :link="route('contact')">
            Empezamos por un diagnóstico con números y fecha, y de ahí sale lo que se construye.
        </x-frost.cta>
    </section>

    <section class="max-w-6xl mx-auto px-fluid-sm pb-fluid-lg">
        <a href="{{ route('portfolio.index') }}" class="text-sm font-semibold hover:text-frost-muted transition">← Todo el portafolio</a>
    </section>

    <div class="border-t border-frost-border">
        <x-portfolio.tech-ribbon />
    </div>
</x-layouts.app>
