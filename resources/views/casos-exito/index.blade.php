<x-layouts.app>
    <x-slot:title>
        Casos de éxito | Urano Dev
    </x-slot:title>

    <section class="max-w-6xl mx-auto px-fluid-sm py-fluid-lg text-center md:text-left">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
            <div class="md:col-span-8">
                <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Casos de éxito</span>
                <h1 class="text-4xl md:text-6xl font-bold tracking-tight mt-3 mb-6 leading-[1.05]">
                    Lo que entregamos,<br>contado por dentro.
                </h1>
                <p class="text-lg md:text-xl text-frost-muted max-w-xl leading-relaxed">
                    Cada caso cuenta el diagnóstico con el que empezó el proyecto, lo que el sitio
                    tiene hoy y lo que el negocio debe seguir haciendo. Solo proyectos en producción,
                    publicados con permiso del cliente.
                </p>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-fluid-sm py-8">
        <div class="border-t border-frost-border pt-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($casos as $slug => $caso)
                    <a href="{{ route('casos-exito.show', $slug) }}"
                       class="group border border-frost-border p-8 bg-white hover:border-frost-dark hover:shadow-sm transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-frost-muted">
                                {{ $caso['lugar'] }} · {{ $caso['entregado'] }}
                            </span>
                            <h2 class="text-2xl font-bold tracking-tight mt-2 group-hover:text-frost-muted transition-colors">
                                {{ $caso['nombre'] }}
                            </h2>
                            <p class="text-sm text-frost-muted mt-1">{{ $caso['giro'] }}</p>
                            <p class="text-sm text-frost-muted leading-relaxed mt-4">{{ $caso['resumen'] }}</p>
                        </div>

                        <div class="flex items-center text-xs font-semibold text-frost-dark mt-6 group-hover:translate-x-1 transition-transform duration-300">
                            Ver el caso <span class="ml-1">→</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-fluid-sm py-8">
        <x-frost.cta
            title="¿Quieres que el tuyo sea el siguiente?"
            buttonText="Cuéntanos qué necesitas"
            :link="route('contact')">
            Empezamos por un diagnóstico con números y fecha, y de ahí sale lo que se construye.
        </x-frost.cta>
    </section>
</x-layouts.app>
