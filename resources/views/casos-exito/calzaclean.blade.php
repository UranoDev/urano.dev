<x-layouts.app>
    <x-slot:title>
        {{ $caso['meta_title'] }}
    </x-slot:title>

    {{-- Portada del caso --}}
    <section class="max-w-6xl mx-auto px-fluid-sm py-fluid-lg">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
            <div class="md:col-span-9">
                <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Caso de éxito</span>

                <h1 class="text-4xl md:text-6xl font-bold tracking-tight mt-3 mb-6 leading-[1.05]">
                    {{ $caso['nombre'] }}
                </h1>

                <p class="text-lg md:text-xl text-frost-muted max-w-2xl leading-relaxed">
                    {{ $caso['resumen'] }}
                </p>
            </div>
        </div>

        <dl class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-12 border-t border-frost-border pt-8">
            <div>
                <dt class="text-[10px] font-bold uppercase tracking-widest text-frost-muted">Giro</dt>
                <dd class="text-sm mt-1">{{ $caso['giro'] }}</dd>
            </div>
            <div>
                <dt class="text-[10px] font-bold uppercase tracking-widest text-frost-muted">Dónde</dt>
                <dd class="text-sm mt-1">{{ $caso['lugar'] }}</dd>
            </div>
            <div>
                <dt class="text-[10px] font-bold uppercase tracking-widest text-frost-muted">Entregado</dt>
                <dd class="text-sm mt-1">{{ $caso['entregado'] }}</dd>
            </div>
            <div>
                <dt class="text-[10px] font-bold uppercase tracking-widest text-frost-muted">Sitio</dt>
                <dd class="text-sm mt-1">
                    <a href="{{ $caso['sitio'] }}" target="_blank" rel="noopener noreferrer" class="underline underline-offset-4 hover:text-frost-muted transition">
                        calzaclean.com
                    </a>
                </dd>
            </div>
        </dl>
    </section>

    {{-- 1. El diagnóstico y el método --}}
    <section class="max-w-6xl mx-auto px-fluid-sm py-fluid-md border-t border-frost-border">
        <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Dónde estaba el negocio</span>
        <h2 class="text-3xl md:text-4xl font-bold tracking-tight mt-2 mb-6">El punto de partida</h2>

        <p class="text-base text-frost-muted max-w-2xl leading-relaxed mb-10">
            El proyecto no empezó con un diseño: empezó midiendo. Estas son las cifras del 6 de
            septiembre de 2026 — la línea base contra la que se mide todo lo que venga después, y la
            razón por la que las decisiones que siguieron se pueden defender con datos.
        </p>

        <div class="overflow-x-auto border border-frost-border bg-white">
            <table class="w-full text-sm">
                <caption class="sr-only">Auditoría de los perfiles públicos de CalzaClean, medida el 6 de septiembre de 2026</caption>
                <tbody>
                    <tr class="border-b border-frost-border">
                        <th scope="row" class="text-left font-medium px-6 py-3">Seguidores en Instagram</th>
                        <td class="px-6 py-3 text-right font-mono text-frost-dark">50, contra 70 seguidos</td>
                    </tr>
                    <tr class="border-b border-frost-border">
                        <th scope="row" class="text-left font-medium px-6 py-3">Publicaciones en 10 meses</th>
                        <td class="px-6 py-3 text-right font-mono text-frost-dark">11 — 1.1 al mes, con tres meses en blanco</td>
                    </tr>
                    <tr class="border-b border-frost-border">
                        <th scope="row" class="text-left font-medium px-6 py-3">Seguidores en Facebook</th>
                        <td class="px-6 py-3 text-right font-mono text-frost-dark">5</td>
                    </tr>
                    <tr class="border-b border-frost-border">
                        <th scope="row" class="text-left font-medium px-6 py-3">Reseñas</th>
                        <td class="px-6 py-3 text-right font-mono text-frost-dark">0</td>
                    </tr>
                    <tr class="border-b border-frost-border">
                        <th scope="row" class="text-left font-medium px-6 py-3">Enlaces en la biografía</th>
                        <td class="px-6 py-3 text-right font-mono text-frost-dark">0</td>
                    </tr>
                    <tr>
                        <th scope="row" class="text-left font-medium px-6 py-3">Perfil de empresa en Google</th>
                        <td class="px-6 py-3 text-right font-mono text-frost-dark">no existía</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p class="text-xs text-frost-muted mt-3">
            Medido el 6 de septiembre de 2026 sobre los perfiles públicos del negocio.
        </p>

    </section>

    {{-- 2. Las secciones y páginas reales --}}
    <section class="max-w-6xl mx-auto px-fluid-sm py-fluid-md border-t border-frost-border">
        <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Qué se construyó</span>
        <h2 class="text-3xl md:text-4xl font-bold tracking-tight mt-2 mb-6">Las secciones y páginas reales</h2>

        <p class="text-base text-frost-muted max-w-2xl leading-relaxed mb-10">
            Lo que existe hoy en calzaclean.com, revisado contra el sitio publicado el 7 de
            septiembre de 2026.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="border border-frost-border p-6 bg-white">
                <h3 class="text-lg font-semibold tracking-tight mb-4">Páginas</h3>
                <ul class="space-y-3 text-sm text-frost-muted leading-relaxed">
                    <li><span class="text-frost-dark font-medium">Portada</span> — una sola página con anclas.</li>
                    <li><span class="text-frost-dark font-medium">Precios</span> — dirección propia, para mandarla por mensaje sin explicar nada. Reemplazó la captura de pantalla que antes se reenviaba.</li>
                    <li><span class="text-frost-dark font-medium">Resultados</span> — la galería completa de pares, que va agregando de a cuatro.</li>
                    <li><span class="text-frost-dark font-medium">Cuidado de tenis</span> — guía breve por material.</li>
                    <li><span class="text-frost-dark font-medium">Aviso de privacidad</span> y <span class="text-frost-dark font-medium">términos y condiciones</span>.</li>
                </ul>
            </div>

            <div class="border border-frost-border p-6 bg-white">
                <h3 class="text-lg font-semibold tracking-tight mb-4">Secciones de la portada</h3>
                <ul class="space-y-3 text-sm text-frost-muted leading-relaxed">
                    <li><span class="text-frost-dark font-medium">Servicios y precios</span> — la lista completa, con los extras agrupados al final.</li>
                    <li><span class="text-frost-dark font-medium">Resultados</span> — el antes y el después de cada par, con una manija que se arrastra para comparar.</li>
                    <li><span class="text-frost-dark font-medium">Cómo funciona</span> — cuatro pasos, y ahí mismo la recolección a domicilio con sus dos zonas y su costo.</li>
                    <li><span class="text-frost-dark font-medium">Materiales</span> — qué se le hace a cada uno y con qué.</li>
                    <li><span class="text-frost-dark font-medium">Preguntas frecuentes</span>.</li>
                    <li><span class="text-frost-dark font-medium">Contacto</span> — horarios, domicilio y redes.</li>
                </ul>
                <p class="text-xs text-frost-muted leading-relaxed mt-4 pt-4 border-t border-frost-border">
                    La sección de testimonios existe y hoy no se dibuja: todavía no hay ninguno
                    publicado. El sitio no enseña secciones vacías.
                </p>
            </div>

            <div class="border border-frost-border p-6 bg-white">
                <h3 class="text-lg font-semibold tracking-tight mb-4">El panel</h3>
                <ul class="space-y-3 text-sm text-frost-muted leading-relaxed">
                    <li><span class="text-frost-dark font-medium">Trabajos</span> — subir las dos fotos desde el celular, con el par recién terminado enfrente. Es la pantalla que se usa cada semana.</li>
                    <li><span class="text-frost-dark font-medium">Precios</span> — tabla editable, con vista previa de cómo queda publicada.</li>
                    <li><span class="text-frost-dark font-medium">Preguntas y testimonios</span>.</li>
                    <li><span class="text-frost-dark font-medium">Ajustes</span> — se abre en tres: contacto y redes, datos del negocio con sus zonas de recolección, y un aviso que enciende una franja arriba del sitio.</li>
                </ul>
            </div>
        </div>

        @if($caso['capturas'])
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12">
                @foreach($caso['capturas'] as $captura)
                    <figure>
                        <img src="{{ $captura['imagen'] }}" alt="{{ $captura['pie'] }}" class="w-full border border-frost-border">
                        <figcaption class="text-xs text-frost-muted mt-2">{{ $captura['pie'] }}</figcaption>
                    </figure>
                @endforeach
            </div>
        @endif
    </section>

    {{-- 3. Las acciones que el negocio tiene que seguir --}}
    <section class="max-w-6xl mx-auto px-fluid-sm py-fluid-md border-t border-frost-border">
        <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Qué sigue</span>
        <h2 class="text-3xl md:text-4xl font-bold tracking-tight mt-2 mb-6">Las acciones que el negocio tiene que seguir</h2>

        <p class="text-base text-frost-muted max-w-2xl leading-relaxed mb-10">
            El diagnóstico dejó quince acciones. El sitio resolvió cuatro y habilitó otras dos; las
            nueve restantes dependen de una persona, no de una entrega. Las tres que más rinden, en
            orden:
        </p>

        <ol class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <li class="border border-frost-border p-6 bg-white">
                <span class="text-[10px] font-bold uppercase tracking-widest text-frost-muted">Primero</span>
                <h3 class="text-lg font-semibold tracking-tight mt-2">Abrir el perfil de empresa en Google</h3>
                <p class="text-sm text-frost-muted leading-relaxed mt-2">
                    Gratis, y es lo que hace que aparezcan al buscar «limpieza de tenis San Juan del
                    Río». Con el domicilio, los horarios y el teléfono ya definidos, llenarlo toma
                    una tarde.
                </p>
            </li>
            <li class="border border-frost-border p-6 bg-white">
                <span class="text-[10px] font-bold uppercase tracking-widest text-frost-muted">Segundo</span>
                <h3 class="text-lg font-semibold tracking-tight mt-2">Juntar diez reseñas</h3>
                <p class="text-sm text-frost-muted leading-relaxed mt-2">
                    Un mensaje a cada cliente anterior con la liga directa. Hoy son cero, y nadie
                    entrega sus tenis a un desconocido sin una sola prueba.
                </p>
            </li>
            <li class="border border-frost-border p-6 bg-white">
                <span class="text-[10px] font-bold uppercase tracking-widest text-frost-muted">Tercero</span>
                <h3 class="text-lg font-semibold tracking-tight mt-2">Publicar tres veces por semana, sin excepción</h3>
                <p class="text-sm text-frost-muted leading-relaxed mt-2">
                    Después de tres meses en silencio, la constancia vale más que la calidad de
                    cualquier pieza suelta.
                </p>
            </li>
        </ol>

        <h3 class="text-2xl font-bold tracking-tight mt-16 mb-6">Las quince, con su estado</h3>

        <div class="overflow-x-auto border border-frost-border bg-white">
            <table class="w-full text-sm">
                <caption class="sr-only">Las quince acciones del diagnóstico y su estado al 7 de septiembre de 2026</caption>
                <thead>
                    <tr class="border-b border-frost-border bg-frost-light">
                        <th scope="col" class="text-left text-[10px] font-bold uppercase tracking-widest text-frost-muted px-4 py-3">#</th>
                        <th scope="col" class="text-left text-[10px] font-bold uppercase tracking-widest text-frost-muted px-4 py-3">Acción</th>
                        <th scope="col" class="text-left text-[10px] font-bold uppercase tracking-widest text-frost-muted px-4 py-3">Estado</th>
                        <th scope="col" class="text-left text-[10px] font-bold uppercase tracking-widest text-frost-muted px-4 py-3">Qué pasó</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($caso['acciones'] as $accion)
                        <tr class="border-b border-frost-border last:border-b-0 align-top">
                            <td class="px-4 py-3 font-mono text-xs text-frost-muted whitespace-nowrap">{{ $accion['numero'] }}</td>
                            <td class="px-4 py-3 font-medium">{{ $accion['accion'] }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span @class([
                                    'inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-1',
                                    'bg-frost-dark text-white' => in_array($accion['estado'], ['Hecha', 'Decidida'], true),
                                    'border border-frost-dark text-frost-dark' => $accion['estado'] === 'Habilitada',
                                    'border border-frost-border text-frost-muted' => $accion['estado'] === 'Pendiente',
                                ])>{{ $accion['estado'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-frost-muted">{{ $accion['nota'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p class="text-sm text-frost-muted max-w-2xl leading-relaxed mt-6">
            Nueve de las quince son de operación —publicar seguido, pedir reseñas, abrir la ficha de
            Google— y ninguna se resuelve construyendo software. Un sitio no vende solo.
        </p>
    </section>

    {{-- De dónde salen las cifras --}}
    <section class="max-w-6xl mx-auto px-fluid-sm py-fluid-md border-t border-frost-border">
        <h2 class="text-2xl font-bold tracking-tight mb-6">De dónde salen las cifras</h2>

        <div class="overflow-x-auto border border-frost-border bg-white">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-frost-border bg-frost-light">
                        <th scope="col" class="text-left text-[10px] font-bold uppercase tracking-widest text-frost-muted px-6 py-3">Fuente</th>
                        <th scope="col" class="text-left text-[10px] font-bold uppercase tracking-widest text-frost-muted px-6 py-3">Fecha</th>
                        <th scope="col" class="text-left text-[10px] font-bold uppercase tracking-widest text-frost-muted px-6 py-3">Qué aporta</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-frost-border align-top">
                        <td class="px-6 py-3 font-medium">Diagnóstico de marca, versión 1</td>
                        <td class="px-6 py-3 whitespace-nowrap text-frost-muted">6 de septiembre de 2026</td>
                        <td class="px-6 py-3 text-frost-muted">Las cifras de la auditoría y las quince acciones.</td>
                    </tr>
                    <tr class="border-b border-frost-border align-top">
                        <td class="px-6 py-3 font-medium">Diagnóstico de marca, versión 2</td>
                        <td class="px-6 py-3 whitespace-nowrap text-frost-muted">7 de septiembre de 2026</td>
                        <td class="px-6 py-3 text-frost-muted">El estado de cada acción y las decisiones ya cerradas.</td>
                    </tr>
                    <tr class="border-b border-frost-border align-top">
                        <td class="px-6 py-3 font-medium">Muestreo del archivo del logo</td>
                        <td class="px-6 py-3 whitespace-nowrap text-frost-muted">6 de septiembre de 2026</td>
                        <td class="px-6 py-3 text-frost-muted">Los dos azules y sus medidas de contraste.</td>
                    </tr>
                    <tr class="align-top">
                        <td class="px-6 py-3 font-medium">El sitio publicado</td>
                        <td class="px-6 py-3 whitespace-nowrap text-frost-muted">7 de septiembre de 2026</td>
                        <td class="px-6 py-3 text-frost-muted">Las páginas, las secciones y las pantallas del panel.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-fluid-sm py-8">
        <x-frost.cta
            title="¿Empezamos por medir el tuyo?"
            buttonText="Cuéntanos qué necesitas"
            :link="route('contact')">
            El diagnóstico va primero: números con su fecha, y de ahí sale lo que conviene construir.
        </x-frost.cta>
    </section>

    <section class="max-w-6xl mx-auto px-fluid-sm pb-fluid-lg">
        <a href="{{ route('casos-exito.index') }}" class="text-sm font-semibold hover:text-frost-muted transition">← Todos los casos</a>
    </section>
</x-layouts.app>
