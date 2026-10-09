<x-layouts.app>
    <x-slot:title>
        Portafolio | Urano Dev
    </x-slot:title>
    <x-slot:description>
        Proyectos en producción: qué resuelve cada uno, con qué se construyó y capturas del sitio real.
    </x-slot:description>
    <x-slot:image>{{ asset('images/og/portafolio.png') }}</x-slot:image>

    <section class="max-w-6xl mx-auto px-fluid-sm py-fluid-lg text-center md:text-left">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
            <div class="md:col-span-8">
                <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Portafolio</span>
                <h1 class="text-4xl md:text-6xl font-bold tracking-tight mt-3 mb-6 leading-[1.05]">
                    Lo que hemos<br>construido.
                </h1>
                <p class="text-lg md:text-xl text-frost-muted max-w-xl leading-relaxed">
                    Cada proyecto cuenta con qué se construyó, qué resuelve por dentro y qué páginas
                    y secciones tiene. Capturas del sitio real, no maquetas.
                </p>
            </div>
        </div>
    </section>

    @if ($projects->isNotEmpty())
        <section class="max-w-6xl mx-auto px-fluid-sm py-8">
            <div class="border-t border-frost-border pt-12 space-y-16">
                @foreach ($projects as $project)
                    <x-portfolio.project-card :project="$project" />
                @endforeach
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
</x-layouts.app>
