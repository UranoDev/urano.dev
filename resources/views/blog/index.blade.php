<x-layouts.app>
    <x-slot:title>Blog — Urano Dev</x-slot:title>

    <section class="max-w-4xl mx-auto px-fluid-sm py-fluid-md">
        <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Nuestro Blog</span>
        <h1 class="text-3xl md:text-5xl font-bold tracking-tight mt-2 mb-8">Artículos y Tutoriales</h1>

        @if ($posts->isEmpty())
            <div class="text-center py-12 border border-dashed border-frost-border">
                <p class="text-frost-muted text-sm">No hay publicaciones disponibles por el momento.</p>
            </div>
        @else
            <div class="space-y-12">
                @foreach ($posts as $post)
                    <article class="border-b border-frost-border pb-10 last:border-0 last:pb-0">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            @if ($post->cover_image)
                                <div class="md:col-span-1 rounded overflow-hidden border border-frost-border h-32 bg-frost-light">
                                    <a href="{{ route('blog.show', $post->slug) }}">
                                        <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover hover:scale-105 transition duration-300">
                                    </a>
                                </div>
                            @endif
                            
                            <div class="{{ $post->cover_image ? 'md:col-span-3' : 'md:col-span-4' }}">
                                <header class="mb-4">
                                    <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">
                                        {{ $post->published_at ? $post->published_at->format('d/m/Y') : '' }}
                                    </span>
                                    <h2 class="text-2xl md:text-3xl font-bold tracking-tight mt-2 hover:text-frost-muted transition">
                                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                    </h2>
                                    @if ($post->tags && $post->tags->isNotEmpty())
                                        <div class="flex flex-wrap gap-2 mt-3">
                                            @foreach ($post->tags as $tag)
                                                <span class="bg-frost-light text-frost-dark text-xs px-2 py-0.5 rounded border border-frost-border">
                                                    #{{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </header>
                                <div class="text-base text-frost-muted mb-4 leading-relaxed">
                                    {{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 200) }}
                                </div>
                                <div>
                                    <a href="{{ route('blog.show', $post->slug) }}" class="text-sm font-semibold hover:underline text-frost-dark">
                                        Leer artículo &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @endif
    </section>
</x-layouts.app>
