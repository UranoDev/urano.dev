<?php

use App\Enums\IdeaStatus;
use App\Models\Idea;
use App\Models\Vote;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Ideas')] #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    #[Validate('required|string|max:255')]
    public string $suggestionTitle = '';

    #[Validate('required|string|max:5000')]
    public string $suggestionBody = '';

    public bool $showSuggestionForm = false;

    public function toggleVote(Idea $idea): void
    {
        if (! Auth::check()) {
            session()->put('url.intended', route('ideas.public'));
            $this->redirect(route('login'));

            return;
        }

        abort_unless(Auth::user()->hasVerifiedEmail(), 403);
        abort_unless($idea->isApproved(), 403);

        $existingVote = Vote::where('user_id', Auth::id())
            ->where('idea_id', $idea->id)
            ->first();

        if ($existingVote) {
            $existingVote->delete();
            $idea->decrement('votes_count');
        } else {
            Vote::create([
                'user_id' => Auth::id(),
                'idea_id' => $idea->id,
            ]);
            $idea->increment('votes_count');
        }
    }

    public function suggestIdea(): void
    {
        if (! Auth::check()) {
            session()->put('url.intended', route('ideas.public'));
            $this->redirect(route('login'));

            return;
        }

        abort_unless(Auth::user()->hasVerifiedEmail(), 403);

        $this->validate();

        Idea::create([
            'user_id' => Auth::id(),
            'title' => $this->suggestionTitle,
            'body' => $this->suggestionBody,
            'status' => IdeaStatus::Pending,
        ]);

        $this->reset('suggestionTitle', 'suggestionBody', 'showSuggestionForm');
        $this->resetValidation();

        Flux::toast(variant: 'success', text: 'Tu idea fue enviada y está pendiente de aprobación.');
    }

    public function openSuggestionForm(): void
    {
        $this->showSuggestionForm = true;
    }

    public function closeSuggestionForm(): void
    {
        $this->reset('suggestionTitle', 'suggestionBody', 'showSuggestionForm');
        $this->resetValidation();
    }

    #[Computed]
    public function ideas()
    {
        return Idea::where('status', IdeaStatus::Approved)
            ->orderByDesc('votes_count')
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    #[Computed]
    public function userIdeas()
    {
        if (! Auth::check()) {
            return collect();
        }

        return Idea::where('user_id', Auth::id())
            ->whereIn('status', [IdeaStatus::Pending, IdeaStatus::Rejected])
            ->orderByDesc('created_at')
            ->get();
    }

    #[Computed]
    public function votedIdeaIds(): array
    {
        if (! Auth::check()) {
            return [];
        }

        return Auth::user()->votes()->pluck('idea_id')->toArray();
    }
}; ?>

<section class="max-w-2xl mx-auto px-fluid-sm py-fluid-lg">
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight mb-2">Ideas</h1>
        <p class="text-frost-muted">Vota por las ideas que más te gustan o sugiere una nueva.</p>
    </div>

    {{-- Banner para usuarios no autenticados --}}
    @guest
        <div class="mb-6 p-4 border border-frost-border bg-frost-light text-sm">
            <a href="{{ route('login', ['intended' => route('ideas.public')]) }}" class="font-semibold underline">Inicia sesión</a>
            o <a href="{{ route('register', ['intended' => route('ideas.public')]) }}" class="font-semibold underline">regístrate</a>
            para votar o sugerir una nueva idea.
        </div>
    @endguest

    {{-- Banner para usuarios no verificados --}}
    @auth
        @unless (Auth::user()->hasVerifiedEmail())
            <div class="mb-6 p-4 border border-amber-200 bg-amber-50 text-sm text-amber-800">
                Verifica tu correo electrónico para poder votar y sugerir ideas.
            </div>
        @endunless
    @endauth

    {{-- Botón / formulario para sugerir idea --}}
    @auth
        @if (Auth::user()->hasVerifiedEmail())
            @if (! $showSuggestionForm)
                <div class="mb-6">
                    <button
                        wire:click="openSuggestionForm"
                        class="bg-frost-dark text-white text-sm font-semibold px-5 py-2.5 hover:bg-opacity-90 transition"
                    >
                        Sugerir una idea
                    </button>
                </div>
            @else
                <div class="mb-6 p-5 border border-frost-border">
                    <h2 class="font-semibold text-frost-dark mb-4">Sugerir una nueva idea</h2>
                    <form wire:submit="suggestIdea" class="space-y-4">
                        <div>
                            <label for="suggestionTitle" class="block text-sm font-medium mb-1">Título</label>
                            <input
                                id="suggestionTitle"
                                type="text"
                                wire:model="suggestionTitle"
                                class="w-full border border-frost-border px-3 py-2 text-sm focus:outline-none focus:border-frost-dark"
                                placeholder="Describe tu idea en una frase"
                            />
                            @error('suggestionTitle')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="suggestionBody" class="block text-sm font-medium mb-1">Descripción</label>
                            <textarea
                                id="suggestionBody"
                                wire:model="suggestionBody"
                                rows="3"
                                class="w-full border border-frost-border px-3 py-2 text-sm focus:outline-none focus:border-frost-dark"
                                placeholder="Explica un poco más tu idea..."
                            ></textarea>
                            @error('suggestionBody')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex gap-3">
                            <button
                                type="submit"
                                class="bg-frost-dark text-white text-sm font-semibold px-5 py-2 hover:bg-opacity-90 transition"
                            >
                                Enviar idea
                            </button>
                            <button
                                type="button"
                                wire:click="closeSuggestionForm"
                                class="text-sm font-medium text-frost-muted hover:text-frost-dark transition"
                            >
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        @endif
    @endauth

    {{-- Ideas del usuario (pendientes o rechazadas) --}}
    @if ($this->userIdeas->isNotEmpty())
        <div class="mb-8">
            <h2 class="text-sm font-semibold text-frost-muted uppercase tracking-wider mb-3">Tus ideas sugeridas</h2>
            <div class="space-y-3">
                @foreach ($this->userIdeas as $userIdea)
                    <div
                        wire:key="user-idea-{{ $userIdea->id }}"
                        class="p-4 border {{ $userIdea->isRejected() ? 'border-red-200 bg-red-50' : 'border-amber-200 bg-amber-50' }}"
                    >
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-semibold {{ $userIdea->isRejected() ? 'text-red-800' : 'text-amber-800' }}">
                                {{ $userIdea->title }}
                            </h3>
                            @if ($userIdea->isPending())
                                <span class="text-xs font-medium px-2 py-0.5 bg-amber-200 text-amber-800">Pendiente</span>
                            @else
                                <span class="text-xs font-medium px-2 py-0.5 bg-red-200 text-red-800">Rechazada</span>
                            @endif
                        </div>
                        <p class="text-sm {{ $userIdea->isRejected() ? 'text-red-700' : 'text-amber-700' }} line-clamp-2">
                            {{ $userIdea->body }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Lista de ideas aprobadas --}}
    <div class="space-y-3">
        @forelse ($this->ideas as $idea)
            @php $hasVoted = in_array($idea->id, $this->votedIdeaIds); @endphp
            <div class="flex items-start gap-4 p-4 border border-frost-border hover:border-frost-dark transition" wire:key="idea-{{ $idea->id }}">
                <div class="flex flex-col items-center gap-0.5 min-w-[2.5rem]">
                    <button
                        wire:click="toggleVote({{ $idea->id }})"
                        wire:loading.attr="disabled"
                        wire:target="toggleVote({{ $idea->id }})"
                        class="group flex flex-col items-center gap-0.5 focus:outline-none"
                        title="{{ $hasVoted ? 'Quitar voto' : 'Votar' }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            class="w-5 h-5 transition-colors {{ $hasVoted ? 'text-frost-dark' : 'text-frost-muted group-hover:text-frost-dark' }}">
                            <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0110 17z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-semibold {{ $hasVoted ? 'text-frost-dark' : 'text-frost-muted' }}">
                            {{ $idea->votes_count }}
                        </span>
                    </button>
                </div>

                <div class="flex-1 min-w-0">
                    <h2 class="font-semibold text-frost-dark">{{ $idea->title }}</h2>
                    <p class="text-sm text-frost-muted mt-1 line-clamp-2">{{ $idea->body }}</p>
                </div>
            </div>
        @empty
            <p class="text-frost-muted text-center py-12">No hay ideas todavía.</p>
        @endforelse
    </div>

    @if ($this->ideas->hasPages())
        <div class="mt-6">
            {{ $this->ideas->links() }}
        </div>
    @endif
</section>
