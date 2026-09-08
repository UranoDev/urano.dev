<x-layouts.app>
    <x-slot:title>
        Página no encontrada | Urano Dev
    </x-slot:title>

    <section class="max-w-6xl mx-auto px-fluid-sm py-fluid-lg">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
            <div class="md:col-span-8">
                <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Error 404</span>

                <h1 class="text-4xl md:text-6xl font-bold tracking-tight mt-3 mb-6 leading-[1.05]">
                    Esta dirección<br>no existe.
                </h1>

                <p class="text-lg text-frost-muted max-w-xl leading-relaxed mb-8">
                    Puede que la página se haya movido o que el enlace tenga un error de dedo.
                </p>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('home') }}"
                       class="bg-frost-dark text-white font-semibold text-sm px-6 py-3 hover:bg-opacity-90 transition text-center">
                        Ir al inicio
                    </a>

                    <a href="{{ route('contact') }}"
                       class="border border-frost-border font-semibold text-sm px-6 py-3 hover:border-frost-dark transition text-center">
                        Escribirnos →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-fluid-sm py-8">
        <div class="border-t border-frost-border pt-12">
            <h2 class="text-xs font-bold uppercase tracking-widest text-frost-muted mb-6">A dónde sí se llega</h2>

            <ul class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <li><a href="{{ route('services.index') }}" class="hover:underline">Servicios</a></li>
                <li><a href="{{ route('casos-exito.index') }}" class="hover:underline">Casos de éxito</a></li>
                <li><a href="{{ route('blog.index') }}" class="hover:underline">Blog</a></li>
                <li><a href="{{ route('nosotros') }}" class="hover:underline">Nosotros</a></li>
            </ul>
        </div>
    </section>
</x-layouts.app>
