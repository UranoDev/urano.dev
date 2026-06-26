<x-layouts.app>
    <x-slot:title>Links — Urano Dev</x-slot:title>

    <section class="max-w-md mx-auto px-4 py-12 text-center">
        @php
            $firstLink = $links->first();
            $owner = $firstLink?->owner;
        @endphp

        @if ($owner)
            @if ($owner->avatar)
                <img src="{{ asset('storage/' . $owner->avatar) }}" alt="{{ $owner->name }}" class="w-20 h-20 rounded-full mx-auto mb-4 object-cover border border-frost-border" data-test="owner-avatar">
            @else
                <div class="w-20 h-20 rounded-full bg-frost-dark text-white flex items-center justify-center font-bold text-2xl uppercase mx-auto mb-4 border border-frost-border" data-test="owner-avatar-placeholder">
                    {{ $owner->initials() }}
                </div>
            @endif
        @else
            <div class="w-20 h-20 bg-frost-dark rounded-full mx-auto mb-4" data-test="owner-avatar-placeholder"></div>
        @endif
        <h1 class="text-2xl font-bold tracking-tight">Urano Dev</h1>
        <p class="text-xs text-frost-muted mt-1 mb-8">Todo en un solo lugar.</p>

        @if ($links->isEmpty())
            <p class="text-sm text-frost-muted">No hay links disponibles por ahora.</p>
        @else
            <div class="space-y-3">
                @foreach ($links as $link)
                    <a
                        href="{{ route('links.click', $link) }}"
                        target="_blank"
                        rel="noopener noreferrer"                        class="block w-full border border-frost-border bg-white text-sm font-medium py-4 px-6 hover:border-frost-dark hover:bg-frost-light transition text-center"
                    >
                        {{ $link->title }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="mt-12 text-xs text-frost-muted tracking-tight">
            urano.dev
        </div>
    </section>
</x-layouts.app>
