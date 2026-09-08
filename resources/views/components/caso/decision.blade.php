@props(['titulo'])

@php
    // Una decisión se cuenta en tres movimientos: qué restricción manda, qué se
    // hizo para resolverla y qué salió. Si falta uno, la página no se dibuja a
    // medias — deja de construirse y el hueco se ve en la primera prueba.
    $movimientos = [
        'Estrategia' => $estrategia ?? null,
        'Proceso' => $proceso ?? null,
        'Resultado' => $resultado ?? null,
    ];

    foreach ($movimientos as $nombre => $contenido) {
        if ($contenido === null || $contenido->isEmpty()) {
            throw new InvalidArgumentException(
                "La decisión «{$titulo}» no tiene {$nombre}. Una decisión que no se puede contar en los tres movimientos no entra al caso."
            );
        }
    }
@endphp

<article {{ $attributes->class(['border-t border-frost-border pt-8 mt-8 first:mt-0 first:border-t-0 first:pt-0']) }}>
    <h3 class="text-xl font-bold tracking-tight">{{ $titulo }}</h3>

    <dl class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-6">
        @foreach($movimientos as $nombre => $contenido)
            <div>
                <dt class="text-[10px] font-bold uppercase tracking-widest text-frost-muted">{{ $nombre }}</dt>
                <dd class="text-sm text-frost-muted leading-relaxed mt-2">{{ $contenido }}</dd>
            </div>
        @endforeach
    </dl>
</article>
