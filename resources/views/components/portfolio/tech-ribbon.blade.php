@php
    $technologies = \App\Models\Technology::orderBy('id')->get();
    $loop = $technologies->concat($technologies);
@endphp

<div>
    <div class="max-w-6xl mx-auto px-fluid-sm py-8" style="overflow:hidden">
        <div
            class="tech-ribbon-track flex gap-8 w-max"
            style="-webkit-mask-image:linear-gradient(to right,transparent,black 40px,black calc(100% - 40px),transparent);mask-image:linear-gradient(to right,transparent,black 40px,black calc(100% - 40px),transparent)"
        >
            @foreach ($loop as $technology)
                <div class="shrink-0 flex flex-col items-center gap-2 group" style="width:44px">
                    <x-portfolio.tech-icon :slug="$technology->slug" class="size-7" />
                    <span class="text-[10px] text-frost-muted opacity-0 group-hover:opacity-100 transition whitespace-nowrap">{{ $technology->name }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .tech-ribbon-track {
        animation: tech-ribbon-scroll 90s linear infinite;
    }

    .tech-ribbon-track:hover {
        animation-play-state: paused;
    }

    @keyframes tech-ribbon-scroll {
        from { transform: translateX(0); }
        to { transform: translateX(-50%); }
    }

    @media (prefers-reduced-motion: reduce) {
        .tech-ribbon-track {
            animation: none;
        }
    }
</style>
