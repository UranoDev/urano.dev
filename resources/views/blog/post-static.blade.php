<x-layouts.app :isStatic="true" :whatsappUrl="route('blog.show', $post->slug)">
    <x-slot:title>{{ $post->title }} — Urano Dev</x-slot:title>

    <article class="max-w-4xl mx-auto px-fluid-sm py-fluid-md">
        <header class="mb-8 border-b border-frost-border pb-8">
            <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">
                {{ $post->published_at ? $post->published_at->format('d/m/Y') : '' }}
            </span>
            <h1 class="text-3xl md:text-5xl font-bold tracking-tight mt-2 mb-4">{{ $post->title }}</h1>
            @if ($post->tags && $post->tags->isNotEmpty())
                <div class="flex flex-wrap gap-2 mt-4">
                    @foreach ($post->tags as $tag)
                        <span class="bg-frost-light text-frost-dark text-xs px-2 py-1 rounded border border-frost-border">
                            #{{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            @endif
        </header>

        @if ($post->cover_image)
            <div class="mb-8 rounded overflow-hidden border border-frost-border bg-frost-light">
                <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}" class="w-full h-auto max-h-[450px] object-cover">
            </div>
        @endif

        <div class="post-content text-base text-frost-dark leading-relaxed">
            {!! $content !!}
        </div>

        @if ($post->author)
            <div class="mt-12 p-6 bg-frost-light rounded-xl border border-frost-border flex flex-col sm:flex-row items-center sm:items-start gap-4" data-test="author-section">
                @if ($post->author->avatar)
                    <img src="{{ asset('storage/' . $post->author->avatar) }}" alt="{{ $post->author->name }}" class="w-16 h-16 rounded-full object-cover border border-frost-border" data-test="author-avatar">
                @else
                    <div class="w-16 h-16 rounded-full bg-frost-muted text-white flex items-center justify-center font-bold text-xl uppercase border border-frost-border" data-test="author-avatar-placeholder">
                        {{ $post->author->initials() }}
                    </div>
                @endif
                <div class="text-center sm:text-left flex-1">
                    <span class="text-xs font-semibold text-frost-muted uppercase tracking-widest">Escrito por</span>
                    <h3 class="text-lg font-bold text-frost-dark mt-0.5" data-test="author-name">{{ $post->author->name }}</h3>
                    @if ($post->author->bio)
                        <p class="text-sm text-frost-muted mt-2 leading-relaxed" data-test="author-bio">{{ $post->author->bio }}</p>
                    @else
                        <p class="text-sm text-frost-muted mt-2 italic" data-test="author-bio">Sin biografía disponible.</p>
                    @endif
                </div>
            </div>
        @endif

        <div class="border-t border-frost-border mt-12 pt-6">
            <a href="/" class="text-sm font-semibold hover:underline">&larr; Volver al inicio</a>
        </div>
    </article>

    <style>
        .post-content p { margin-bottom: 1.5rem; line-height: 1.75; }
        .post-content h2 { font-size: 1.75rem; margin-top: 2rem; margin-bottom: 1rem; font-weight: 700; }
        .post-content h3 { font-size: 1.5rem; margin-top: 1.75rem; margin-bottom: 0.75rem; font-weight: 600; }
        .post-content ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.5rem; }
        .post-content ol { list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 1.5rem; }
        .post-content li { margin-bottom: 0.5rem; }
        .post-content blockquote { border-left: 4px solid var(--color-frost-border); padding-left: 1rem; color: var(--color-frost-muted); font-style: italic; margin-bottom: 1.5rem; }
        .post-content pre { background: var(--color-frost-light); border: 1px solid var(--color-frost-border); padding: 1rem; overflow-x: auto; border-radius: 6px; margin-bottom: 1.5rem; }
        .post-content code { font-family: 'JetBrains Mono', monospace; font-size: 0.9em; }
    </style>
</x-layouts.app>
