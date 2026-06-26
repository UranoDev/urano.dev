<?php

use App\Enums\IdeaStatus;
use App\Models\Idea;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Ideas')] class extends Component {
    use WithPagination;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|string|max:5000')]
    public string $body = '';

    public ?int $editingId = null;
    public ?int $deletingId = null;

    public function openCreate(): void
    {
        $this->reset('title', 'body', 'editingId');
        $this->resetValidation();
        $this->modal('idea-form')->show();
    }

    public function openEdit(Idea $idea): void
    {
        $this->editingId = $idea->id;
        $this->title = $idea->title;
        $this->body = $idea->body;
        $this->resetValidation();
        $this->modal('idea-form')->show();
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId) {
            Idea::findOrFail($this->editingId)->update([
                'title' => $this->title,
                'body' => $this->body,
            ]);

            Flux::toast(variant: 'success', text: 'Idea actualizada.');
        } else {
            Idea::create([
                'user_id' => Auth::id(),
                'title' => $this->title,
                'body' => $this->body,
            ]);

            Flux::toast(variant: 'success', text: 'Idea creada.');
        }

        $this->modal('idea-form')->close();
        $this->reset('title', 'body', 'editingId');
        $this->resetPage();
    }

    public function approve(Idea $idea): void
    {
        $idea->approve();
        Flux::toast(variant: 'success', text: 'Idea aprobada.');
    }

    public function reject(Idea $idea): void
    {
        $idea->reject();
        Flux::toast(variant: 'success', text: 'Idea rechazada.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->modal('delete-idea')->show();
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            Idea::findOrFail($this->deletingId)->delete();
            Flux::toast(variant: 'success', text: 'Idea eliminada.');
            $this->deletingId = null;
            $this->modal('delete-idea')->close();
            $this->resetPage();
        }
    }

    #[Computed]
    public function ideas()
    {
        return Idea::with('user')->latest()->paginate(10);
    }
}; ?>

<section class="w-full">
    <div class="flex items-center justify-between mb-6">
            <flux:heading size="xl">Ideas</flux:heading>
            <flux:button variant="primary" wire:click="openCreate" icon="plus">
                Nueva idea
            </flux:button>
        </div>

        <flux:table :paginate="$this->ideas">
            <flux:table.columns>
                <flux:table.column>Título</flux:table.column>
                <flux:table.column>Estado</flux:table.column>
                <flux:table.column>Votos</flux:table.column>
                <flux:table.column>Autor</flux:table.column>
                <flux:table.column>Fecha</flux:table.column>
                <flux:table.column align="end">Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->ideas as $idea)
                    <flux:table.row :key="$idea->id">
                        <flux:table.cell variant="strong" class="max-w-xs truncate">
                            {{ $idea->title }}
                        </flux:table.cell>

                        <flux:table.cell>
                            @if ($idea->isPending())
                                <flux:badge color="amber" size="sm" inset="top bottom">Pendiente</flux:badge>
                            @elseif ($idea->isApproved())
                                <flux:badge color="green" size="sm" inset="top bottom">Aprobada</flux:badge>
                            @else
                                <flux:badge color="red" size="sm" inset="top bottom">Rechazada</flux:badge>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell>{{ $idea->votes_count }}</flux:table.cell>

                        <flux:table.cell>{{ $idea->user?->name ?? '—' }}</flux:table.cell>

                        <flux:table.cell>{{ $idea->created_at->format('d/m/Y') }}</flux:table.cell>

                        <flux:table.cell align="end">
                            <div class="flex items-center justify-end gap-2">
                                @if ($idea->isPending())
                                    <flux:button
                                        size="sm"
                                        variant="ghost"
                                        icon="check"
                                        wire:click="approve({{ $idea->id }})"
                                        wire:confirm="¿Aprobar esta idea?"
                                    >
                                        Aprobar
                                    </flux:button>
                                    <flux:button
                                        size="sm"
                                        variant="ghost"
                                        icon="x-mark"
                                        wire:click="reject({{ $idea->id }})"
                                        wire:confirm="¿Rechazar esta idea?"
                                    >
                                        Rechazar
                                    </flux:button>
                                @endif

                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    icon="pencil"
                                    wire:click="openEdit({{ $idea->id }})"
                                >
                                    Editar
                                </flux:button>

                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    icon="trash"
                                    wire:click="confirmDelete({{ $idea->id }})"
                                >
                                    Eliminar
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6">
                            <flux:text class="text-center py-8">No hay ideas todavía.</flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        {{-- Modal crear / editar idea --}}
        <flux:modal name="idea-form" class="min-w-[32rem]">
            <div class="space-y-6">
                <flux:heading size="lg">
                    {{ $editingId ? 'Editar idea' : 'Nueva idea' }}
                </flux:heading>

                <form wire:submit="save" class="space-y-4">
                    <flux:field>
                        <flux:label>Título</flux:label>
                        <flux:input wire:model="title" type="text" autofocus />
                        <flux:error name="title" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Descripción</flux:label>
                        <flux:textarea wire:model="body" rows="5" />
                        <flux:error name="body" />
                    </flux:field>

                    <div class="flex gap-2 justify-end pt-2">
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancelar</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary">
                            {{ $editingId ? 'Guardar cambios' : 'Crear idea' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- Modal confirmar eliminación --}}
        <flux:modal name="delete-idea" class="min-w-[22rem]">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">¿Eliminar esta idea?</flux:heading>
                    <flux:text class="mt-2">
                        Esta acción no se puede deshacer. Se eliminarán también los votos asociados.
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
