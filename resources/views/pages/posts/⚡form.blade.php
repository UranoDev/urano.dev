<?php

use App\Models\Post;
use App\Models\Tag;
use App\Services\SlugService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Post')] class extends Component {
    use WithFileUploads;
    #[Locked]
    public ?int $editingId = null;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:255')]
    public string $slug = '';

    #[Validate('required|string')]
    public string $content = '';

    #[Validate('nullable|string|max:500')]
    public string $excerpt = '';

    #[Validate('required|in:draft,published,scheduled,archived')]
    public string $status = 'draft';

    public ?string $published_at = null;

    #[Validate('nullable|image|max:2048')]
    public $cover_image_file = null;

    public ?string $cover_image = null;

    /** @var array<string> */
    public array $selectedTags = [];

    public string $tagInput = '';

    public function removeCurrentImage(): void
    {
        $this->cover_image = null;
    }
    public function mount(?Post $post = null): void
    {
        if ($post?->exists) {
            $this->authorize('update', $post);

            $this->editingId = $post->id;
            $this->title = $post->title;
            $this->slug = $post->slug;
            $this->content = $post->content;
            $this->excerpt = $post->excerpt ?? '';
            $this->status = $post->status;
            $this->published_at = $post->published_at?->format('Y-m-d\TH:i');
            $this->cover_image = $post->cover_image;
            $this->selectedTags = $post->tags->pluck('name')->toArray();
            return;
        }

        $this->authorize('create', Post::class);
    }

    public function addTag(string $name): void
    {
        $name = trim($name);

        if ($name === '' || in_array($name, $this->selectedTags)) {
            $this->tagInput = '';

            return;
        }

        $this->selectedTags[] = $name;
        $this->tagInput = '';
    }

    public function removeTag(string $name): void
    {
        $this->selectedTags = array_values(array_filter($this->selectedTags, fn ($t) => $t !== $name));
    }

    #[Computed]
    public function tagSuggestions(): array
    {
        if (strlen($this->tagInput) < 1) {
            return [];
        }

        return Tag::where('name', 'like', '%' . $this->tagInput . '%')
            ->whereNotIn('name', $this->selectedTags)
            ->orderBy('name')
            ->limit(10)
            ->pluck('name')
            ->toArray();
    }
    public function save(): void
    {
        $slugService = app(SlugService::class);

        if ($this->editingId) {
            $post = Post::findOrFail($this->editingId);
            $this->authorize('update', $post);
        } else {
            $this->authorize('create', Post::class);
        }

        $this->validate();

        if ($this->cover_image_file) {
            if ($this->editingId && $post->cover_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($post->cover_image);
            }
            $this->cover_image = $this->cover_image_file->store('images', 'public');
        } elseif ($this->editingId && !$this->cover_image && $post->cover_image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($post->cover_image);
        }
        $baseSlug = $this->slug ?: $this->title;
        $finalSlug = $slugService->generateUniqueSlug($baseSlug, Post::class, $this->editingId);

        $data = [
            'title' => $this->title,
            'slug' => $finalSlug,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'cover_image' => $this->cover_image,
        ];

        $tagIds = collect($this->selectedTags)
            ->filter(fn ($name) => trim($name) !== '')
            ->map(function ($name) {
                $slug = Str::slug($name);

                return Tag::firstOrCreate(['slug' => $slug], ['name' => $name, 'slug' => $slug])->id;
            })
            ->toArray();

        if ($this->editingId) {
            $post->tags()->sync($tagIds);
            $post->update($data);
        } else {
            $data['user_id'] = Auth::id();
            $post = Post::create($data);
            $post->tags()->sync($tagIds);

            if ($post->status === 'published') {
                $post->load('tags', 'author');
                $staticPath = app(\App\Services\PostStaticGenerator::class)->generate($post);
                $post->withoutEvents(function () use ($post, $staticPath) {
                    $post->update(['static_path' => $staticPath]);
                });
            }
        }

        Flux::toast(variant: 'success', text: $this->editingId ? 'Post actualizado.' : 'Post creado.');
        if ($this->slug && $this->slug !== $finalSlug) {
            Flux::toast(variant: 'warning', text: "El slug ya existe. Se ha usado: {$finalSlug}");
        }

        $this->redirectRoute('posts.index', navigate: true);
    }

    public function cancel(): void
    {
        $this->redirectRoute('posts.index', navigate: true);
    }
}; ?>

<section class="w-full max-w-5xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">
                {{ $editingId ? 'Editar post' : 'Nuevo post' }}
            </flux:heading>
            <flux:text class="mt-2 text-frost-muted">
                {{ $editingId ? 'Actualiza el contenido y la publicacion del post.' : 'Crea un nuevo borrador o publica el post directamente.' }}
            </flux:text>
        </div>

        <flux:button variant="ghost" icon="arrow-left" href="{{ route('posts.index') }}" wire:navigate>
            Volver
        </flux:button>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <flux:field class="md:col-span-2">
                <flux:label>Titulo</flux:label>
                <flux:input wire:model="title" type="text" placeholder="Titulo del post" />
                <flux:error name="title" />
            </flux:field>

            <flux:field class="md:col-span-2">
                <flux:label>Slug (opcional)</flux:label>
                <flux:input wire:model="slug" type="text" placeholder="mi-post-interesante" />
                <flux:description>Se generara automaticamente si se deja vacio.</flux:description>
                <flux:error name="slug" />
            </flux:field>
        </div>

        <flux:field>
            <flux:label>Contenido (Markdown)</flux:label>
            <livewire:ui.markdown-editor wire:model="content" placeholder="# Empieza a escribir tu gran post..." />
            <flux:error name="content" />
        </flux:field>

        <flux:field>
            <flux:label>Extracto (opcional)</flux:label>
            <flux:textarea wire:model="excerpt" rows="3" placeholder="Breve resumen..." />
            <flux:error name="excerpt" />
        </flux:field>

        <flux:field>
            <flux:label>Etiquetas (opcional)</flux:label>
            <div
                x-data="{
                    open: false,
                    handleKey(e) {
                        if ((e.key === 'Enter' || e.key === ',') && $wire.tagInput.trim()) {
                            e.preventDefault();
                            $wire.addTag($wire.tagInput);
                            this.open = false;
                        }
                        if (e.key === 'Escape') { this.open = false; }
                    }
                }"
                class="relative"
            >
                <div class="flex flex-wrap gap-2 mb-2">
                    @foreach ($selectedTags as $tag)
                        <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 dark:bg-zinc-700 px-2.5 py-0.5 text-sm text-zinc-700 dark:text-zinc-300">
                            {{ $tag }}
                            <button type="button" wire:click="removeTag('{{ addslashes($tag) }}')" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                                <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </span>
                    @endforeach
                </div>

                <flux:input
                    wire:model.live.debounce.200ms="tagInput"
                    type="text"
                    placeholder="Escribe una etiqueta y presiona Enter o coma..."
                    @keydown="handleKey($event)"
                    @focus="open = true"
                    @blur="setTimeout(() => open = false, 150)"
                    autocomplete="off"
                />

                <div
                    x-show="open && $wire.tagSuggestions.length > 0"
                    class="absolute z-20 mt-1 w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-lg"
                >
                    @foreach ($this->tagSuggestions as $suggestion)
                        <button
                            type="button"
                            wire:click="addTag('{{ addslashes($suggestion) }}')"
                            @mousedown.prevent
                            class="block w-full text-left px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 first:rounded-t-lg last:rounded-b-lg"
                        >
                            {{ $suggestion }}
                        </button>
                    @endforeach
                </div>
            </div>
            <flux:description>Presiona Enter o coma para agregar. Puedes escribir etiquetas nuevas libremente.</flux:description>
        </flux:field>

        <flux:field>
            <flux:label>Imagen de portada (opcional)</flux:label>
            
            <div class="mt-2 space-y-4">
                @if ($cover_image_file)
                    <div class="relative w-48 h-32 border border-frost-border overflow-hidden rounded bg-frost-light">
                        <img src="{{ $cover_image_file->temporaryUrl() }}" class="w-full h-full object-cover">
                        <button type="button" wire:click="$set('cover_image_file', null)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition shadow">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                @elseif ($cover_image)
                    <div class="relative w-48 h-32 border border-frost-border overflow-hidden rounded bg-frost-light">
                        <img src="{{ asset('storage/' . $cover_image) }}" class="w-full h-full object-cover">
                        <button type="button" wire:click="removeCurrentImage" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition shadow">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                @endif

                <div class="flex items-center gap-4">
                    <input type="file" wire:model="cover_image_file" id="cover_image_file" class="hidden" accept="image/*" />
                    <flux:button type="button" onclick="document.getElementById('cover_image_file').click()">
                        Seleccionar imagen
                    </flux:button>
                    <flux:description>Recomendado: ratio 16:9, formato JPG/PNG, máx 2MB.</flux:description>
                </div>
            </div>
            <flux:error name="cover_image_file" />
        </flux:field>
        <div class="grid gap-4 md:grid-cols-2">
            <flux:field>
                <flux:label>Estado</flux:label>
                <flux:select wire:model="status">
                    <flux:select.option value="draft">Borrador</flux:select.option>
                    <flux:select.option value="published">Publicado</flux:select.option>
                    <flux:select.option value="scheduled">Programado</flux:select.option>
                    <flux:select.option value="archived">Archivado</flux:select.option>
                </flux:select>
                <flux:error name="status" />
            </flux:field>

            <flux:field>
                <flux:label>Fecha de publicacion</flux:label>
                <flux:input wire:model="published_at" type="datetime-local" />
                <flux:error name="published_at" />
            </flux:field>
        </div>

        <div class="flex flex-col-reverse gap-2 pt-2 sm:flex-row sm:justify-end">
            <flux:button type="button" variant="ghost" wire:click="cancel">
                Cancelar
            </flux:button>
            <flux:button type="submit" variant="primary">
                {{ $editingId ? 'Guardar cambios' : 'Crear post' }}
            </flux:button>
        </div>
    </form>
</section>
