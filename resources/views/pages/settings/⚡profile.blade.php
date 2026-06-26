<?php

use App\Concerns\ProfileValidationRules;
/* @chisel-email-verification */
use Illuminate\Contracts\Auth\MustVerifyEmail;
/* @end-chisel-email-verification */
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules;
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $bio = '';
    public $avatar_file = null;
    public ?string $avatar = null;
    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->bio = $user->bio ?? '';
        $this->avatar = $user->avatar;    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(\App\Models\User::class)->ignore($user->id)],
            'bio' => ['nullable', 'string', 'max:1000'],
            'avatar_file' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($this->avatar_file) {
            if ($this->avatar) {
                Storage::disk('public')->delete($this->avatar);
            }
            $this->avatar = $this->avatar_file->store('avatars', 'public');
        }

        $user->forceFill([
            'name' => $this->name,
            'email' => $this->email,
            'bio' => $this->bio,
            'avatar' => $this->avatar,
        ]);
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->reset('avatar_file');

        Flux::toast(variant: 'success', text: __('Profile updated.'));
    }

    /**
     * Remove the current user's avatar.
     */
    public function removeAvatar(): void
    {
        $user = Auth::user();

        if ($this->avatar) {
            Storage::disk('public')->delete($this->avatar);
        }

        $this->avatar = null;
        $user->forceFill([
            'avatar' => null,
        ]);
        $user->save();

        Flux::toast(variant: 'success', text: __('Avatar removed.'));
    }
    /* @chisel-email-verification */
    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
    /* @end-chisel-email-verification */
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Actualiza tu información personal, foto de perfil y biografía.')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6" enctype="multipart/form-data">
            <flux:field>
                <flux:label>{{ __('Foto de perfil / Avatar') }}</flux:label>
                
                <div class="mt-2 flex items-center gap-6">
                    @if ($avatar_file)
                        <div class="relative w-20 h-20 border border-zinc-200 dark:border-zinc-700 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <img src="{{ $avatar_file->temporaryUrl() }}" class="w-full h-full object-cover" data-test="avatar-preview">
                            <button type="button" wire:click="$set('avatar_file', null)" class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition shadow" data-test="cancel-avatar-button">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    @elseif ($avatar)
                        <div class="relative w-20 h-20 border border-zinc-200 dark:border-zinc-700 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <img src="{{ asset('storage/' . $avatar) }}" class="w-full h-full object-cover" data-test="avatar-current">
                            <button type="button" wire:click="removeAvatar" class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition shadow" data-test="remove-avatar-button">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    @else
                        <div class="flex items-center justify-center w-20 h-20 rounded-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-400 dark:text-zinc-500" data-test="avatar-placeholder">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    @endif

                    <div class="space-y-1">
                        <input type="file" wire:model="avatar_file" id="avatar_file" class="hidden" accept="image/*" data-test="avatar-input" />
                        <flux:button type="button" size="sm" onclick="document.getElementById('avatar_file').click()" data-test="select-avatar-button">
                            {{ __('Seleccionar foto') }}
                        </flux:button>
                        <flux:description class="text-xs">{{ __('JPG o PNG, máx. 2MB. Se recortará en círculo.') }}</flux:description>
                    </div>
                </div>
                <flux:error name="avatar_file" />
            </flux:field>
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                {{-- @chisel-email-verification --}}
                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
                {{-- @end-chisel-email-verification --}}
            </div>

            <flux:field>
                <flux:textarea wire:model="bio" :label="__('Biografía')" rows="4" placeholder="{{ __('Cuéntanos un poco sobre ti...') }}" data-test="bio-input" />
                <flux:error name="bio" />
            </flux:field>
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                        {{ __('Save') }}
                    </flux:button>
                </div>

            </div>
        </form>

        {{-- @chisel-email-verification --}}
        @if ($this->showDeleteUser)
        {{-- @end-chisel-email-verification --}}
            <livewire:pages::settings.delete-user-form />
        {{-- @chisel-email-verification --}}
        @endif
        {{-- @end-chisel-email-verification --}}
    </x-pages::settings.layout>
</section>
