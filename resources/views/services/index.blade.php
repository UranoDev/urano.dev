<x-layouts.app>
    <x-slot:title>
        Servicios de Tecnología para PYMEs y Turismo | Urano Dev
    </x-slot:title>

    <section class="max-w-6xl mx-auto px-fluid-sm py-fluid-lg text-center md:text-left">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
            <div class="md:col-span-8">
                <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Nuestros Servicios</span>
                <h1 class="text-4xl md:text-6xl font-bold tracking-tight mt-3 mb-6 leading-[1.05]">
                    Soluciones para<br>tu negocio.
                </h1>
                <p class="text-lg md:text-xl text-frost-muted max-w-xl mb-8 leading-relaxed">
                    Desarrollamos tecnología a la medida para PYMEs y empresas turísticas. Reservaciones, facturación CFDI, pagos y más.
                </p>
                <a href="https://wa.me/{{ config('services.whatsapp.number') }}?text={{ urlencode('quiero saber más de ' . request()->url()) }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-block bg-frost-dark text-white font-semibold text-sm px-6 py-3 hover:bg-opacity-90 transition text-center">
                    Solicitar Cotización
                </a>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-fluid-sm py-8">
        <div class="border-t border-frost-border pt-12">
            <x-frost.features>
                @foreach($services as $service)
                    <x-frost.feature-box title="{{ $service->title }}">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-frost-muted">{{ $service->category }}</span>
                        <p class="mt-2">{{ Str::limit($service->hero_desc, 120) }}</p>
                        <a href="{{ route('services.show', $service->slug) }}" class="hover:underline text-frost-dark font-semibold block mt-4 text-xs tracking-tight">Saber más →</a>
                    </x-frost.feature-box>
                @endforeach
            </x-frost.features>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-fluid-sm py-8">
        <x-frost.cta
            title="¿Listo para automatizar tu negocio?"
            buttonText="Hablemos de tu proyecto"
            :link="'https://wa.me/' . config('services.whatsapp.number') . '?text=' . urlencode('quiero saber más de ' . request()->url())">
            Ayudamos a PYMEs y empresas turísticas en todo México a digitalizar sus operaciones de reservaciones, cobros y facturación.
        </x-frost.cta>
    </section>
</x-layouts.app>
