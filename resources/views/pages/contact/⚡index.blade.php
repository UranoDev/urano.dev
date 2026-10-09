<?php

use App\Enums\ProjectTimeframe;
use App\Models\ProjectInquiry;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Cuéntanos qué quieres construir')] #[Layout('components.layouts.app')] class extends Component {
    public string $name = '';

    public string $email = '';

    public string $company = '';

    public string $projectDescription = '';

    public string $timeframe = '';

    /**
     * Campo oculto a la vista: una persona no lo ve ni lo llena; los bots que
     * rellenan todo el formulario sí.
     */
    public string $website = '';

    public bool $submitted = false;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:255',
            'company' => 'nullable|string|max:120',
            'projectDescription' => ['required', 'string', 'min:20', 'max:5000', function (string $attribute, mixed $value, Closure $fail) {
                if (str_word_count($value, 0, 'áéíóúüñÁÉÍÓÚÜÑ') < 3) {
                    $fail('Descríbelo con algunas palabras: qué hace el sistema y para quién.');
                }
            }],
            'timeframe' => ['required', 'string', 'in:'.implode(',', array_column(ProjectTimeframe::cases(), 'value'))],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Escribe tu nombre.',
            'email.required' => 'Escribe un correo donde podamos responderte.',
            'email.email' => 'Ese correo no tiene un formato válido.',
            'projectDescription.required' => 'Cuéntanos qué quieres construir.',
            'projectDescription.min' => 'Danos un poco más de detalle: al menos 20 caracteres.',
            'timeframe.required' => 'Elige para cuándo lo necesitas.',
            'timeframe.in' => 'Elige una de las opciones de la lista.',
        ];
    }

    public function submit(): void
    {
        // Al bot se le muestra la misma confirmación que a una persona, para
        // que no tenga cómo saber que su envío se descartó.
        if ($this->website !== '') {
            $this->reset('name', 'email', 'company', 'projectDescription', 'timeframe', 'website');
            $this->submitted = true;

            return;
        }

        $validated = $this->validate();

        $limite = 'contacto:'.request()->ip();

        if (RateLimiter::tooManyAttempts($limite, 3)) {
            $this->addError('projectDescription', 'Ya recibimos varios mensajes desde tu conexión. Escríbenos por WhatsApp para seguir.');

            return;
        }

        RateLimiter::hit($limite, 3600);

        ProjectInquiry::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'company' => $validated['company'] ?: null,
            'project_description' => $validated['projectDescription'],
            'timeframe' => $validated['timeframe'],
        ]);

        $this->reset('name', 'email', 'company', 'projectDescription', 'timeframe');
        $this->resetValidation();

        $this->submitted = true;
    }

    public function startAnother(): void
    {
        $this->submitted = false;
    }

    /**
     * @return array<int, ProjectTimeframe>
     */
    public function timeframes(): array
    {
        return ProjectTimeframe::cases();
    }

    public function whatsappUrl(): string
    {
        return 'https://wa.me/'.config('services.whatsapp.number')
            .'?text='.urlencode('quiero saber más de '.route('contact'));
    }
}; ?>

<section class="max-w-2xl mx-auto px-fluid-sm py-fluid-lg">
    @if ($submitted)
        <div class="border border-frost-border p-fluid-md">
            <h1 class="text-3xl font-bold tracking-tight mb-3">Tu mensaje quedó registrado</h1>
            <p class="text-frost-muted mb-6">
                Lo leemos y respondemos al correo que dejaste.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <button
                    type="button"
                    wire:click="startAnother"
                    class="bg-frost-dark text-white font-semibold text-sm px-6 py-3 hover:bg-opacity-90 transition text-center"
                >
                    Enviar otro mensaje
                </button>
                <a href="{{ $this->whatsappUrl() }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="border border-frost-border font-semibold text-sm px-6 py-3 hover:border-frost-dark transition text-center">
                    Continuar por WhatsApp
                </a>
            </div>
        </div>
    @else
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold tracking-tight mb-3">Cuéntanos qué quieres construir</h1>
            <p class="text-frost-muted">
                Describe el desarrollo que tienes en mente y para cuándo lo necesitas. Se guarda junto con tus datos de contacto.
            </p>
        </div>

        <form wire:submit="submit" class="space-y-5">
            <div>
                <label for="name" class="block text-sm font-medium mb-1">Nombre</label>
                <input
                    id="name"
                    type="text"
                    wire:model="name"
                    class="w-full border border-frost-border px-3 py-2 text-sm focus:outline-none focus:border-frost-dark"
                />
                @error('name')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium mb-1">Correo</label>
                <input
                    id="email"
                    type="email"
                    wire:model="email"
                    class="w-full border border-frost-border px-3 py-2 text-sm focus:outline-none focus:border-frost-dark"
                />
                @error('email')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="company" class="block text-sm font-medium mb-1">
                    Empresa o proyecto <span class="text-frost-muted font-normal">(opcional)</span>
                </label>
                <input
                    id="company"
                    type="text"
                    wire:model="company"
                    class="w-full border border-frost-border px-3 py-2 text-sm focus:outline-none focus:border-frost-dark"
                />
                @error('company')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="projectDescription" class="block text-sm font-medium mb-1">¿Qué quieres construir?</label>
                <textarea
                    id="projectDescription"
                    wire:model="projectDescription"
                    rows="5"
                    class="w-full border border-frost-border px-3 py-2 text-sm focus:outline-none focus:border-frost-dark"
                    placeholder="Qué hace el sistema, quién lo va a usar y qué problema resuelve."
                ></textarea>
                @error('projectDescription')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="hidden" aria-hidden="true">
                <label for="website">Sitio web</label>
                <input id="website" type="text" wire:model="website" tabindex="-1" autocomplete="off" />
            </div>

            <div>
                <label for="timeframe" class="block text-sm font-medium mb-1">¿Para cuándo?</label>
                <select
                    id="timeframe"
                    wire:model="timeframe"
                    class="w-full border border-frost-border px-3 py-2 text-sm bg-white focus:outline-none focus:border-frost-dark"
                >
                    <option value="">Elige una opción</option>
                    @foreach ($this->timeframes() as $option)
                        <option value="{{ $option->value }}">{{ $option->label() }}</option>
                    @endforeach
                </select>
                @error('timeframe')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center pt-2">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="submit"
                    class="bg-frost-dark text-white font-semibold text-sm px-6 py-3 hover:bg-opacity-90 transition"
                >
                    Enviar
                </button>
                <a href="{{ $this->whatsappUrl() }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="text-sm text-frost-muted hover:text-frost-dark transition underline">
                    O escríbenos por WhatsApp
                </a>
            </div>
        </form>
    @endif
</section>
