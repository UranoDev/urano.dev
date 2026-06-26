<?php

use App\Models\Post;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Posts')] class extends Component {
    use WithPagination;

    public ?int $deletingId = null;

    public function confirmDelete(int $id): void
    {
        $post = Post::findOrFail($id);
        $this->authorize('delete', $post);

        $this->deletingId = $id;
        $this->modal('delete-post')->show();
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        $post = Post::findOrFail($this->deletingId);
        $this->authorize('delete', $post);

        $post->delete();

        Flux::toast(variant: 'success', text: 'Post eliminado.');

        $this->deletingId = null;
        $this->modal('delete-post')->close();
        $this->resetPage();
    }

    #[Computed]
    public function posts()
    {
        $query = Post::with('author')->latest();

        if (Auth::user()->isAuthor()) {
            $query->where('user_id', Auth::id());
        }

        return $query->paginate(10);
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <flux:heading size="xl">Posts</flux:heading>
        <flux:button variant="primary" href="{{ route('posts.create') }}" wire:navigate icon="plus">
            Nuevo post
        </flux:button>
    </div>

    <flux:table :paginate="$this->posts">
        <flux:table.columns>
            <flux:table.column>Titulo</flux:table.column>
            <flux:table.column>Estado</flux:table.column>
            <flux:table.column>Autor</flux:table.column>
            <flux:table.column>Fecha</flux:table.column>
            <flux:table.column align="end">Acciones</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->posts as $post)
                <flux:table.row :key="$post->id">
                    <flux:table.cell variant="strong" class="max-w-xs truncate">
                        {{ $post->title }}
                        <flux:text size="sm" class="block truncate text-frost-muted">{{ $post->slug }}</flux:text>
                    </flux:table.cell>

                    <flux:table.cell>
                        @switch($post->status)
                            @case('published')
                                <flux:badge color="green" size="sm" inset="top bottom">Publicado</flux:badge>
                                @break
                            @case('draft')
                                <flux:badge color="zinc" size="sm" inset="top bottom">Borrador</flux:badge>
                                @break
                            @case('scheduled')
                                <flux:badge color="amber" size="sm" inset="top bottom">Programado</flux:badge>
                                @break
                            @case('archived')
                                <flux:badge color="red" size="sm" inset="top bottom">Archivado</flux:badge>
                                @break
                        @endswitch
                    </flux:table.cell>

                    <flux:table.cell>{{ $post->author->name }}</flux:table.cell>

                    <flux:table.cell>
                        <flux:text size="sm">{{ $post->created_at->format('d/m/Y') }}</flux:text>
                        @if($post->published_at)
                            <flux:text size="xs" class="block text-frost-muted">Pub: {{ $post->published_at->format('d/m/Y H:i') }}</flux:text>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell align="end">
                        <div class="flex items-center justify-end gap-2">
                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="eye"
                                href="{{ route('blog.show', $post->slug) }}"
                                target="_blank"
                            >
                                Ver
                            </flux:button>

                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="pencil"
                                href="{{ route('posts.edit', $post) }}"
                                wire:navigate
                            >
                                Editar
                            </flux:button>

                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="trash"
                                wire:click="confirmDelete({{ $post->id }})"
                            >
                                Eliminar
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5">
                        <flux:text class="py-8 text-center">No hay posts todavia.</flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="delete-post" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Eliminar este post?</flux:heading>
                <flux:text class="mt-2">
                    Esta accion no se puede deshacer. Se eliminaran permanentemente todos los datos asociados.
                </flux:text>
            </div>
            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button variant="danger" wire:click="delete">Eliminar</flux:button>
            </div>
        </div>
    </flux:modal>
</section>
