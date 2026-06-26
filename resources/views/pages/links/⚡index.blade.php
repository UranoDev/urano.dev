<?php

use App\Enums\LinkType;
use App\Models\Link;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Title('Links')] class extends Component {

    #[Validate('required|string|max:255')]
    public string $title = '';

    public ?string $url = null;

    #[Validate('required|in:external,internal')]
    public string $type = 'external';

    public ?string $postId = null;

    #[Validate('nullable|exists:users,id')]
    public ?string $userId = null;
    #[Validate('boolean')]
    public bool $isActive = true;

    #[Validate('integer|min:0')]
    public int $sortOrder = 0;

    public ?int $editingId = null;
    public ?int $deletingId = null;

    public function openCreate(): void
    {
        $this->reset('title', 'url', 'type', 'isActive', 'editingId', 'postId');
        $this->type = 'external';
        $this->isActive = true;
        $this->userId = (string) auth()->id();        $this->sortOrder = (Link::max('sort_order') ?? 0) + 1;
        $this->resetValidation();
        $this->modal('link-form')->show();
    }

    public function openEdit(Link $link): void
    {
        $this->editingId = $link->id;
        $this->title = $link->title;
        $this->url = $link->url;
        $this->type = $link->type->value;
        $this->isActive = $link->is_active;
        $this->sortOrder = $link->sort_order;
        $this->postId = $this->resolveValidPostId($link->post_id);
        $this->userId = $this->resolveValidOwnerId($link->user_id);        $this->resetValidation();
        $this->modal('link-form')->show();
    }

    public function updatedType(): void
    {
        if ($this->type === 'internal') {
            $this->url = null;
            $this->postId = $this->resolveValidPostId($this->postId ? (int) $this->postId : null);
        } else {
            $this->postId = null;
        }
    }

    /**
     * Normaliza el owner al valor que el <select> mostrará realmente.
     *
     * Un <select> nativo siempre muestra una opción; si la propiedad apunta a
     * un valor ausente de las opciones (null o un usuario que no es admin), el
     * navegador auto-muestra el primer admin sin disparar `change`, por lo que
     * la propiedad nunca se sincroniza y se guarda el valor obsoleto. Forzar la
     * propiedad a una opción válida mantiene DOM y estado consistentes.
     */
    private function resolveValidOwnerId(?int $value): ?string
    {
        $admins = $this->admins;

        if ($value !== null && $admins->contains('id', $value)) {
            return (string) $value;
        }

        if ($admins->contains('id', auth()->id())) {
            return (string) auth()->id();
        }

        return $admins->first() ? (string) $admins->first()->id : null;
    }

    /**
     * Normaliza el post asociado al valor que el <select> mostrará realmente
     * (ver explicación en resolveValidOwnerId). Solo aplica a links internos.
     */
    private function resolveValidPostId(?int $value): ?string
    {
        if ($this->type !== 'internal') {
            return null;
        }

        $posts = $this->posts;

        if ($value !== null && $posts->contains('id', $value)) {
            return (string) $value;
        }

        return $posts->first() ? (string) $posts->first()->id : null;
    }
    public function save(): void
    {
        $urlRules = $this->type === 'external' ? 'required|url|max:2048' : 'nullable|url|max:2048';

        $this->validate([
            'title' => 'required|string|max:255',
            'url' => $urlRules,
            'type' => 'required|in:external,internal',
            'isActive' => 'boolean',
            'sortOrder' => 'integer|min:0',
            'postId' => $this->type === 'internal' ? 'required|exists:posts,id' : 'nullable',
            'userId' => 'nullable|exists:users,id',        ]);

        $data = [
            'title' => $this->title,
            'url' => $this->type === 'external' ? $this->url : null,
            'type' => $this->type,
            'is_active' => $this->isActive,
            'sort_order' => $this->sortOrder,
            'post_id' => $this->type === 'internal' ? $this->postId : null,
            'user_id' => $this->userId,        ];

        if ($this->editingId) {
            Link::findOrFail($this->editingId)->update($data);
            Flux::toast(variant: 'success', text: 'Link actualizado.');
        } else {
            Link::create($data);
            Flux::toast(variant: 'success', text: 'Link creado.');
        }

        $this->modal('link-form')->close();
        $this->reset('title', 'url', 'type', 'isActive', 'sortOrder', 'editingId', 'postId', 'userId');    }

    public function handleSort(int $id, int $position): void
    {
        $links = Link::orderBy('sort_order')->orderBy('id')->get();

        $movedLink = $links->firstWhere('id', $id);
        $remaining = $links->reject(fn(Link $link) => $link->id === $id)->values();
        $remaining->splice($position, 0, [$movedLink]);

        $remaining->each(function (Link $link, int $index): void {
            $link->update(['sort_order' => $index]);
        });

        unset($this->links);
    }

    public function toggleActive(Link $link): void
    {
        $link->update(['is_active' => ! $link->is_active]);
        Flux::toast(variant: 'success', text: $link->fresh()->is_active ? 'Link activado.' : 'Link desactivado.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->modal('delete-link')->show();
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            Link::findOrFail($this->deletingId)->delete();
            Flux::toast(variant: 'success', text: 'Link eliminado.');
            $this->deletingId = null;
            $this->modal('delete-link')->close();
        }
    }

    #[Computed]
    public function links()
    {
        return Link::withCount('clicks')->with(['post', 'owner'])->orderBy('sort_order')->orderBy('id')->get();
    }

    #[Computed]
    public function posts()
    {
        return \App\Models\Post::where('status', 'published')->orderBy('title')->get();
    }

    #[Computed]
    public function admins()
    {
        return \App\Models\User::where('role', \App\Enums\Role::Admin)->orderBy('name')->get();    }
}; ?>

<section class="w-full">
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">Links</flux:heading>
        <flux:button variant="primary" wire:click="openCreate" icon="plus">
            Nuevo link
        </flux:button>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column class="w-10"></flux:table.column>
            <flux:table.column>Título</flux:table.column>
            <flux:table.column>URL</flux:table.column>
            <flux:table.column>Tipo</flux:table.column>
            <flux:table.column>Estado</flux:table.column>
            <flux:table.column>Clicks</flux:table.column>
            <flux:table.column align="end">Acciones</flux:table.column>
        </flux:table.columns>

        <flux:table.rows wire:sort="handleSort">
            @forelse ($this->links as $link)
                <flux:table.row :key="$link->id" wire:sort:item="{{ $link->id }}">
                    <flux:table.cell>
                        <div wire:sort:handle class="cursor-grab text-zinc-400 hover:text-zinc-600 active:cursor-grabbing dark:hover:text-zinc-300">
                            <flux:icon.bars-3 class="size-4" />
                        </div>
                    </flux:table.cell>

                    <flux:table.cell variant="strong" class="max-w-xs truncate">
                        {{ $link->title }}
                    </flux:table.cell>

                    <flux:table.cell class="max-w-xs truncate">
                        @if ($link->isInternal() && $link->post_id)
                            <flux:text class="truncate text-sm text-purple-600 font-medium">
                                Interno: {{ $link->post?->title ?? 'Post no encontrado' }}
                            </flux:text>
                        @elseif ($link->url)                            <flux:text class="truncate text-sm">{{ $link->url }}</flux:text>
                        @else
                            <flux:text class="text-zinc-400">—</flux:text>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        @if ($link->isExternal())
                            <flux:badge color="blue" size="sm" inset="top bottom">Externo</flux:badge>
                        @else
                            <flux:badge color="purple" size="sm" inset="top bottom">Interno</flux:badge>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        @if ($link->is_active)
                            <flux:badge color="green" size="sm" inset="top bottom">Activo</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm" inset="top bottom">Inactivo</flux:badge>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>{{ $link->clicks_count }}</flux:table.cell>

                    <flux:table.cell align="end">
                        <div class="flex items-center justify-end gap-2" wire:sort:ignore>
                            <flux:button
                                size="sm"
                                variant="ghost"
                                :icon="$link->is_active ? 'eye-slash' : 'eye'"
                                wire:click="toggleActive({{ $link->id }})"
                            >
                                {{ $link->is_active ? 'Desactivar' : 'Activar' }}
                            </flux:button>

                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="pencil"
                                wire:click="openEdit({{ $link->id }})"
                            >
                                Editar
                            </flux:button>

                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="trash"
                                wire:click="confirmDelete({{ $link->id }})"
                            >
                                Eliminar
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7">
                        <flux:text class="text-center py-8">No hay links todavía.</flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{-- Modal crear / editar link --}}
    <flux:modal name="link-form" class="min-w-[32rem]">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ $editingId ? 'Editar link' : 'Nuevo link' }}
            </flux:heading>

            <form wire:submit="save" class="space-y-4">
                <flux:field>
                    <flux:label>Título</flux:label>
                    <flux:input wire:model="title" type="text" autofocus data-test="link-title" />                    <flux:error name="title" />
                </flux:field>

                <flux:field>
                    <flux:label>Tipo</flux:label>
                    <flux:select wire:model.live="type" data-test="type-select">                        <flux:select.option value="external">Externo</flux:select.option>
                        <flux:select.option value="internal">Interno</flux:select.option>
                    </flux:select>
                    <flux:error name="type" />
                </flux:field>

                <div x-show="$wire.type === 'external'">                    <flux:field>
                        <flux:label>URL</flux:label>
                        <flux:input wire:model="url" type="url" placeholder="https://ejemplo.com" />
                        <flux:error name="url" />
                    </flux:field>
                </div>

                <div x-show="$wire.type === 'internal'">
                    <flux:field>
                        <flux:label>Post Asociado</flux:label>
                        <flux:select wire:model.live="postId" placeholder="Selecciona un post..." data-test="post-select">
                            @foreach ($this->posts as $postItem)
                                <flux:select.option value="{{ $postItem->id }}">{{ $postItem->title }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="postId" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Propietario (Admin)</flux:label>
                    <flux:select wire:model.live="userId" placeholder="Selecciona un administrador..." data-test="owner-select">
                        @foreach ($this->admins as $adminUser)
                            <flux:select.option value="{{ $adminUser->id }}">{{ $adminUser->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="userId" />
                </flux:field>
                <flux:field>
                    <flux:checkbox wire:model="isActive" label="Activo" />
                    <flux:error name="isActive" />
                </flux:field>

                <div class="flex gap-2 justify-end pt-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancelar</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">
                        {{ $editingId ? 'Guardar cambios' : 'Crear link' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    {{-- Modal confirmar eliminación --}}
    <flux:modal name="delete-link" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">¿Eliminar este link?</flux:heading>
                <flux:text class="mt-2">
                    Esta acción no se puede deshacer. Se eliminarán también los clicks registrados.
                </flux:text>
            </div>
            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button variant="danger" wire:click="delete">Eliminar</flux:button>
            </div>
        </div>
    </flux:modal>
</section>
