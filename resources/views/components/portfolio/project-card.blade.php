@props(['project', 'linkable' => true])

@php
    $featured = $project->featuredScreenshot();
    $otherShots = $project->screenshots->reject(fn ($s) => $featured && $s->is($featured))->take(2);
    $services = $project->technologies->filter(fn ($t) => $t->group === \App\Enums\TechnologyGroup::Service);
    $mark = $project->logo_path ?? $project->favicon_path;
@endphp

<article {{ $attributes->class('relative border border-frost-border bg-white') }}>
    @if ($linkable)
        <a href="{{ route('portfolio.show', $project) }}" class="absolute inset-0 z-0" aria-label="Ver el detalle de {{ $project->title }}"></a>
    @endif

    {{-- Encabezado --}}
    <div class="flex flex-wrap items-start justify-between gap-4 p-6 md:p-8 border-b border-frost-border">
        <div>
            <div class="flex items-center gap-3">
                @if ($mark)
                    <img src="{{ asset($mark) }}" alt="" class="h-8 w-auto">
                @endif
                {{-- En la lista cada proyecto es una sección; en su ficha es el título de la página. --}}
                <{{ $linkable ? 'h2' : 'h1' }} class="text-2xl md:text-3xl font-bold tracking-tight">{{ $project->title }}</{{ $linkable ? 'h2' : 'h1' }}>
            </div>
            <p class="text-sm text-frost-muted mt-1 max-w-md">{{ $project->tagline }}</p>
        </div>
        <div class="flex flex-col items-start md:items-end gap-3 shrink-0">
            <span class="text-lg md:text-xl font-bold tracking-tight tabular-nums text-left md:text-right">
                {{ $project->periodLabel() }}
            </span>

            @if ($services->isNotEmpty())
                <div>
                    <p class="text-[9px] font-bold uppercase tracking-wider text-frost-muted text-left md:text-right mb-1">Servicios</p>
                    <div class="flex flex-wrap justify-start md:justify-end gap-1.5">
                        @foreach ($services as $tech)
                            <span class="group relative size-8 border border-frost-border bg-frost-light flex items-center justify-center">
                                <x-portfolio.tech-icon :slug="$tech->slug" class="size-5" />
                                <span class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 whitespace-nowrap bg-frost-dark text-white text-[10px] font-semibold px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                    {{ $tech->name }}
                                </span>
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Capturas --}}
    @if ($featured || $otherShots->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 md:gap-4 p-4 md:p-8 bg-frost-light items-start">
            @if ($featured)
                <div class="md:col-span-2 border border-frost-border bg-white overflow-hidden">
                    <div class="h-5 bg-frost-light border-b border-frost-border flex items-center gap-1 px-2">
                        <span class="size-1.5 rounded-full bg-frost-border"></span>
                        <span class="size-1.5 rounded-full bg-frost-border"></span>
                        <span class="size-1.5 rounded-full bg-frost-border"></span>
                    </div>
                    <img src="{{ asset($featured->path) }}" alt="{{ $featured->alt }}" width="1280" height="800" style="height: 224px;" class="w-full object-cover object-top block">
                </div>
            @endif
            @foreach ($otherShots as $shot)
                <div class="md:col-span-1 border border-frost-border bg-white overflow-hidden">
                    <div class="h-5 bg-frost-light border-b border-frost-border flex items-center gap-1 px-2">
                        <span class="size-1.5 rounded-full bg-frost-border"></span>
                        <span class="size-1.5 rounded-full bg-frost-border"></span>
                        <span class="size-1.5 rounded-full bg-frost-border"></span>
                    </div>
                    <img src="{{ asset($shot->path) }}" alt="{{ $shot->alt }}" width="760" height="660" style="height: 224px;" class="w-full object-cover object-top block" loading="lazy" decoding="async">
                </div>
            @endforeach
        </div>
    @endif

    @if ($project->url)
        <div class="relative z-10 p-4 md:px-8 md:py-4 border-t border-frost-border">
            <a href="{{ $project->url }}" target="_blank" rel="noopener"
               class="relative inline-flex items-center gap-1 text-xs font-semibold text-frost-dark hover:translate-x-1 transition-transform">
                Ver el sitio <span>→</span>
            </a>
        </div>
    @endif
</article>
