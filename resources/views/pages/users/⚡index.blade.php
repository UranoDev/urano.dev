<?php

use App\Enums\Role;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Usuarios')] class extends Component {
    use WithPagination;

    public string $search = '';

    public ?int $editingId = null;
    public ?int $deletingId = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|min:8')]
    public ?string $password = null;

    #[Validate('required|in:author,admin')]
    public string $role = 'author';

    public function openCreate(): void
    {
        $this->reset('name', 'email', 'password', 'role', 'editingId');
        $this->role = 'author';
        $this->resetValidation();
        $this->modal('user-form')->show();
    }

    public function openEdit(User $user): void
    {
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = null;
        $this->role = $user->role->value;
        $this->resetValidation();
        $this->modal('user-form')->show();
    }

    public function save(): void
    {
        $emailRule = $this->editingId
            ? 'required|email|max:255|unique:users,email,' . $this->editingId
            : 'required|email|max:255|unique:users,email';

        $passwordRule = $this->editingId ? 'nullable|string|min:8' : 'required|string|min:8';

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => $emailRule,
            'password' => $passwordRule,
            'role' => 'required|in:author,admin',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editingId) {
            User::findOrFail($this->editingId)->update($data);
            Flux::toast(variant: 'success', text: 'Usuario actualizado.');
        } else {
            $data['email_verified_at'] = now();
            $data['is_active'] = true;
            User::create($data);
            Flux::toast(variant: 'success', text: 'Usuario creado.');
        }

        $this->modal('user-form')->close();
        $this->reset('name', 'email', 'password', 'role', 'editingId');
    }

    public function toggleActive(User $user): void
    {
        if ($user->id === auth()->id()) {
            Flux::toast(variant: 'warning', text: 'No puedes desactivar tu propia cuenta.');

            return;
        }

        $user->update(['is_active' => ! $user->is_active]);
        Flux::toast(variant: 'success', text: $user->fresh()->is_active ? 'Usuario activado.' : 'Usuario desactivado.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->modal('delete-user')->show();
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        $user = User::findOrFail($this->deletingId);

        if ($user->id === auth()->id()) {
            Flux::toast(variant: 'warning', text: 'No puedes eliminar tu propia cuenta.');
            $this->modal('delete-user')->close();

            return;
        }

        $user->delete();
        Flux::toast(variant: 'success', text: 'Usuario eliminado.');
        $this->deletingId = null;
        $this->modal('delete-user')->close();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function users()
    {
        return User::whereIn('role', [Role::Author, Role::Admin])
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            }))
            ->orderBy('name')
            ->paginate(15);
    }
}; ?>

<section class="w-full">
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">Usuarios</flux:heading>
        <flux:button variant="primary" wire:click="openCreate" icon="plus">
            Nuevo usuario
        </flux:button>
    </div>

    <div class="mb-4">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre o email..." icon="magnifying-glass" />
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Nombre</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column>Rol</flux:table.column>
            <flux:table.column>Estado</flux:table.column>
            <flux:table.column align="end">Acciones</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell variant="strong">
                        <div class="flex items-center gap-2">
                            @if ($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" class="size-7 rounded-full object-cover" alt="{{ $user->name }}">
                            @else
                                <div class="size-7 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-xs font-semibold text-zinc-600 dark:text-zinc-300">
                                    {{ $user->initials() }}
                                </div>
                            @endif
                            {{ $user->name }}
                            @if ($user->id === auth()->id())
                                <flux:badge size="sm" color="blue" inset="top bottom">Tú</flux:badge>
                            @endif
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>{{ $user->email }}</flux:table.cell>

                    <flux:table.cell>
                        @if ($user->isAdmin())
                            <flux:badge color="amber" size="sm" inset="top bottom">Admin</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm" inset="top bottom">Autor</flux:badge>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        @if ($user->is_active)
                            <flux:badge color="green" size="sm" inset="top bottom">Activo</flux:badge>
                        @else
                            <flux:badge color="red" size="sm" inset="top bottom">Inactivo</flux:badge>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell align="end">
                        <div class="flex items-center justify-end gap-2">
                            @if ($user->id !== auth()->id())
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    :icon="$user->is_active ? 'eye-slash' : 'eye'"
                                    wire:click="toggleActive({{ $user->id }})"
                                >
                                    {{ $user->is_active ? 'Desactivar' : 'Activar' }}
                                </flux:button>
                            @endif

                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="pencil"
                                wire:click="openEdit({{ $user->id }})"
                            >
                                Editar
                            </flux:button>

                            @if ($user->id !== auth()->id())
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    icon="trash"
                                    wire:click="confirmDelete({{ $user->id }})"
                                >
                                    Eliminar
                                </flux:button>
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5">
                        <flux:text class="text-center py-8">No hay usuarios registrados todavía.</flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">
        {{ $this->users->links() }}
    </div>

    {{-- Modal crear / editar usuario --}}
    <flux:modal name="user-form" class="min-w-[32rem]">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ $editingId ? 'Editar usuario' : 'Nuevo usuario' }}
            </flux:heading>

            <form wire:submit="save" class="space-y-4">
                <flux:field>
                    <flux:label>Nombre</flux:label>
                    <flux:input wire:model="name" type="text" autofocus />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Email</flux:label>
                    <flux:input wire:model="email" type="email" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ $editingId ? 'Nueva contraseña (dejar vacío para no cambiar)' : 'Contraseña' }}</flux:label>
                    <flux:input wire:model="password" type="password" />
                    <flux:error name="password" />
                </flux:field>

                <flux:field>
                    <flux:label>Rol</flux:label>
                    <flux:select wire:model.live="role" data-test="role-select">
                        <flux:select.option value="author">Autor</flux:select.option>
                        <flux:select.option value="admin">Administrador</flux:select.option>
                    </flux:select>
                    <flux:error name="role" />
                </flux:field>

                <div class="flex gap-2 justify-end pt-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancelar</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">
                        {{ $editingId ? 'Guardar cambios' : 'Crear usuario' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    {{-- Modal confirmar eliminación --}}
    <flux:modal name="delete-user" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">¿Eliminar este usuario?</flux:heading>
                <flux:text class="mt-2">
                    Esta acción no se puede deshacer.
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
