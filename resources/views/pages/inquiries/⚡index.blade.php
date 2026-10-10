<?php

use App\Models\ProjectInquiry;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Contactos')] class extends Component {
    use WithPagination;

    public ?int $viewingId = null;

    public ?int $deletingId = null;

    public function openDetail(int $id): void
    {
        $this->viewingId = $id;
        $this->modal('inquiry-detail')->show();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->modal('inquiry-detail')->close();
        $this->modal('delete-inquiry')->show();
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            ProjectInquiry::findOrFail($this->deletingId)->delete();
            Flux::toast(variant: 'success', text: 'Contacto eliminado.');
            $this->deletingId = null;
            $this->viewingId = null;
            $this->modal('delete-inquiry')->close();
        }
    }

    #[Computed]
    public function inquiries()
    {
        return ProjectInquiry::orderByDesc('created_at')->paginate(15);
    }

    #[Computed]
    public function viewing(): ?ProjectInquiry
    {
        return $this->viewingId ? ProjectInquiry::find($this->viewingId) : null;
    }
}; ?>

<section class="w-full">
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">Contactos</flux:heading>
    </div>

    <flux:table :paginate="$this->inquiries">
        <flux:table.columns>
            <flux:table.column>Nombre</flux:table.column>
            <flux:table.column>Correo</flux:table.column>
            <flux:table.column>Empresa</flux:table.column>
            <flux:table.column>Para cuándo</flux:table.column>
            <flux:table.column>Fecha</flux:table.column>
            <flux:table.column align="end">Acciones</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->inquiries as $inquiry)
                <flux:table.row :key="$inquiry->id">
                    <flux:table.cell variant="strong">{{ $inquiry->name }}</flux:table.cell>
                    <flux:table.cell>{{ $inquiry->email }}</flux:table.cell>
                    <flux:table.cell>{{ $inquiry->company ?? '—' }}</flux:table.cell>
                    <flux:table.cell>{{ $inquiry->timeframe->label() }}</flux:table.cell>
                    <flux:table.cell>{{ $inquiry->created_at->format('d/m/Y') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-1">
                            <flux:button size="sm" variant="ghost" wire:click="openDetail({{ $inquiry->id }})">
                                Ver
                            </flux:button>
                            <flux:button size="sm" variant="ghost" icon="trash" wire:click="confirmDelete({{ $inquiry->id }})">
                                Eliminar
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6">
                        <flux:text class="text-center py-8">Todavía no llega ningún contacto.</flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="inquiry-detail" class="min-w-[32rem]">
        @if ($this->viewing)
            <div class="space-y-4">
                <flux:heading size="lg">{{ $this->viewing->name }}</flux:heading>

                <div class="space-y-1 text-sm">
                    <flux:text>{{ $this->viewing->email }}</flux:text>
                    @if ($this->viewing->company)
                        <flux:text>{{ $this->viewing->company }}</flux:text>
                    @endif
                    <flux:text>{{ $this->viewing->timeframe->label() }}</flux:text>
                    <flux:text>{{ $this->viewing->created_at->format('d/m/Y H:i') }}</flux:text>
                </div>

                <flux:separator />

                <p class="text-sm whitespace-pre-line">{{ $this->viewing->project_description }}</p>

                <div class="flex justify-end">
                    <flux:button size="sm" variant="ghost" icon="trash" wire:click="confirmDelete({{ $this->viewing->id }})">
                        Eliminar
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>

    <flux:modal name="delete-inquiry" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">¿Eliminar este contacto?</flux:heading>
                <flux:text class="mt-2">
                    Se borra el mensaje y los datos de quien lo envió. Esta acción no se puede deshacer.
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
