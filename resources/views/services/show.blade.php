<x-layouts.app>
    <x-slot:title>
        {{ $service->meta_title }}
    </x-slot:title>

    <!-- 1. Hero del Servicio -->
    <section class="max-w-6xl mx-auto px-fluid-sm py-fluid-lg text-center md:text-left">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
            <div class="md:col-span-8">
                <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">
                    {{ $service->category }}
                </span>

                <h1 class="text-4xl md:text-6xl font-bold tracking-tight mt-3 mb-6 leading-[1.05]">
                    {!! $service->hero_title !!}
                </h1>

                <p class="text-lg md:text-xl text-frost-muted max-w-xl mb-8 leading-relaxed">
                    {{ $service->hero_desc }}
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    <a href="https://wa.me/{{ config('services.whatsapp.number') }}?text={{ urlencode('quiero saber más de ' . request()->url()) }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="bg-frost-dark text-white font-semibold text-sm px-6 py-3 hover:bg-opacity-90 transition text-center">
                        {{ $service->cta_text }}
                    </a>

                    <a href="https://wa.me/{{ config('services.whatsapp.number') }}?text={{ urlencode('quiero saber más de ' . request()->url()) }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="border border-frost-border font-semibold text-sm px-6 py-3 hover:border-frost-dark transition text-center">
                        Preguntar dudas →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Beneficios Principales (x-frost.features) -->
    <section class="max-w-6xl mx-auto px-fluid-sm py-8">
        <div class="border-t border-frost-border pt-12">
            <h2 class="text-2xl font-bold tracking-tight mb-2">
                {{ $service->benefits_title }}
            </h2>
            <p class="text-sm text-frost-muted mb-8">
                {{ $service->benefits_subtitle }}
            </p>

            <x-frost.features>
                @foreach($service->benefits as $benefit)
                    <x-frost.feature-box title="{{ $benefit['title'] }}">
                        {{ $benefit['desc'] }}
                    </x-frost.feature-box>
                @endforeach
            </x-frost.features>
        </div>
    </section>

    <!-- 3. Frase / Filosofía (x-frost.quote) -->
    @if($service->quote)
        <section class="max-w-4xl mx-auto px-fluid-sm py-8">
            <x-frost.quote author="{{ $service->quote_author }}">
                "{{ $service->quote }}"
            </x-frost.quote>
        </section>
    @endif

    <!-- 4. Características Técnicas / Módulos Incluidos -->
    <section class="max-w-6xl mx-auto px-fluid-sm py-12 border-t border-frost-border">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
            <div class="md:col-span-4">
                <h3 class="text-xl font-bold tracking-tight">
                    {{ $service->modules_title }}
                </h3>
                <p class="text-sm text-frost-muted mt-2">
                    Todo lo necesario para operar de inmediato de manera formal y segura.
                </p>
            </div>
            <div class="md:col-span-8">
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($service->modules as $module)
                        <li class="flex items-center space-x-3 text-sm text-frost-dark">
                            <span class="text-frost-muted text-lg">&bull;</span>
                            <span>{{ $module }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <!-- 5. Navegación a Otros Servicios -->
    <section class="max-w-6xl mx-auto px-fluid-sm py-12 border-t border-frost-border">
        <div class="mb-8">
            <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Explora más soluciones</span>
            <h3 class="text-2xl font-bold tracking-tight mt-2">Otros servicios para tu negocio</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($otherServices as $other)
                <a href="/servicios/{{ $other->slug }}"
                   class="group border border-frost-border p-6 bg-white hover:border-frost-dark hover:shadow-sm transition-all duration-300 flex flex-col justify-between h-40">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-frost-muted">
                            {{ $other->category }}
                        </span>
                        <h4 class="text-lg font-bold text-frost-dark mt-2 group-hover:text-frost-muted transition-colors">
                            {{ $other->title }}
                        </h4>
                    </div>

                    <div class="flex items-center text-xs font-semibold text-frost-dark group-hover:translate-x-1 transition-transform duration-300">
                        Ver detalles <span class="ml-1">→</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- 6. CTA Final Reutilizable (x-frost.cta) -->
    <section class="max-w-6xl mx-auto px-fluid-sm py-8">
        <x-frost.cta
            title="{{ $service->cta_title }}"
            buttonText="Hablemos de tu proyecto"
            :link="'https://wa.me/' . config('services.whatsapp.number') . '?text=' . urlencode('quiero saber más de ' . request()->url())">
            {{ $service->cta_desc }}
        </x-frost.cta>
    </section>
</x-layouts.app>
