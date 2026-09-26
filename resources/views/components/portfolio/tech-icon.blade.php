@props(['slug', 'class' => 'size-5'])

{{--
    Aproximaciones geométricas de cada logo, no los assets oficiales de marca.
    Se reemplazan por los SVG reales de cada proyecto cuando estén a la mano.
--}}
@switch($slug)
    @case('laravel')
        <svg viewBox="0 0 24 24" fill="none" {{ $attributes->class($class) }}>
            <path d="M12 2L22 7.5V16.5L12 22L2 16.5V7.5L12 2Z" fill="#FF2D20" />
            <path d="M12 2L22 7.5L12 13L2 7.5L12 2Z" fill="rgba(255,255,255,0.18)" />
            <path d="M12 13V22L22 16.5V7.5L12 13Z" fill="rgba(0,0,0,0.15)" />
        </svg>
        @break
    @case('livewire')
        <svg viewBox="0 0 24 24" fill="none" {{ $attributes->class($class) }}>
            <path d="M14.5 2.5L5.5 14.5H12.5L10 21.5L19.5 9.5H12.5L14.5 2.5Z" fill="#4E56A6" />
        </svg>
        @break
    @case('flux')
        <svg viewBox="0 0 24 24" fill="none" {{ $attributes->class($class) }}>
            <rect x="6" y="5" width="2.5" height="14" fill="#7C3AED" />
            <rect x="6" y="5" width="12" height="2.5" fill="#7C3AED" />
            <rect x="6" y="11" width="8" height="2.5" fill="#7C3AED" />
        </svg>
        @break
    @case('tailwind')
        <svg viewBox="0 0 24 24" fill="none" {{ $attributes->class($class) }}>
            <path d="M12 6.8c-2.9 0-4.7 1.4-5.5 4.2 1.1-1.4 2.4-1.9 3.8-1.5.8.2 1.4.8 2 1.4 1 1.1 2.2 2.4 4.5 2.4 2.8 0 4.6-1.4 5.5-4.2-1.1 1.4-2.4 1.9-3.8 1.5-.8-.2-1.4-.8-2-1.4-1-1.1-2.2-2.4-4.5-2.4zm-5.5 6.8c-2.8 0-4.6 1.4-5.5 4.2 1.1-1.4 2.4-1.9 3.8-1.5.8.2 1.4.8 2 1.4 1 1.1 2.2 2.4 4.5 2.4 2.8 0 4.6-1.4 5.5-4.2-1.1 1.4-2.4 1.9-3.8 1.5-.8-.2-1.4-.8-2-1.4-1-1.1-2.2-2.4-4.5-2.4z" fill="#06B6D4" />
        </svg>
        @break
    @case('laravel-ai')
        <svg viewBox="0 0 24 24" fill="none" {{ $attributes->class($class) }}>
            <circle cx="12" cy="12" r="9.5" fill="#4D6BFE" />
            <circle cx="12" cy="12" r="3" fill="white" />
            <path d="M12 5.5v2M12 16.5v2M5.5 12h2M16.5 12h2" stroke="rgba(255,255,255,0.5)" stroke-width="1.5" stroke-linecap="round" />
        </svg>
        @break
    @case('pest')
        <svg viewBox="0 0 24 24" fill="none" {{ $attributes->class($class) }}>
            <circle cx="12" cy="12" r="9.5" fill="#16A34A" />
            <path d="M7.5 12l3.5 3.5 5.5-7" stroke="white" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        @break
    @case('phpunit')
        <svg viewBox="0 0 24 24" fill="none" {{ $attributes->class($class) }}>
            <circle cx="12" cy="12" r="9.5" fill="#3C9CD7" />
            <path d="M8 15V9l4 3 4-3v6" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        @break
    @case('mariadb')
        <svg viewBox="0 0 24 24" fill="none" {{ $attributes->class($class) }}>
            <ellipse cx="12" cy="6.5" rx="7.5" ry="2.5" fill="#003545" />
            <path d="M4.5 6.5v4.5c0 1.4 3.4 2.5 7.5 2.5s7.5-1.1 7.5-2.5V6.5c0 1.4-3.4 2.5-7.5 2.5S4.5 7.9 4.5 6.5Z" fill="#003545" />
            <path d="M4.5 11v4.5c0 1.4 3.4 2.5 7.5 2.5s7.5-1.1 7.5-2.5V11c0 1.4-3.4 2.5-7.5 2.5S4.5 12.4 4.5 11Z" fill="#003545" />
            <ellipse cx="12" cy="15.5" rx="7.5" ry="2.5" fill="#C0765A" />
        </svg>
        @break
    @case('deepseek')
        <svg viewBox="0 0 24 24" fill="none" {{ $attributes->class($class) }}>
            <circle cx="12" cy="12" r="9.5" fill="#4D6BFE" />
            <path d="M12 6l1.5 4.5L18 12l-4.5 1.5L12 18l-1.5-4.5L6 12l4.5-1.5L12 6Z" fill="white" />
        </svg>
        @break
    @case('claude')
        <svg viewBox="0 0 24 24" fill="none" {{ $attributes->class($class) }}>
            <circle cx="12" cy="12" r="9.5" fill="#CC785C" />
            <path d="M12 5.5v13M6.7 8l10.6 8M6.7 16l10.6-8" stroke="white" stroke-width="1.6" stroke-linecap="round" />
        </svg>
        @break
    @case('open-library')
        <svg viewBox="0 0 24 24" fill="none" {{ $attributes->class($class) }}>
            <circle cx="12" cy="12" r="9.5" fill="#1B75BB" />
            <path d="M12 8.5c-1.4-1-3.3-1-4.7-.5v7.5c1.4-.5 3.3-.5 4.7.5 1.4-1 3.3-1 4.7-.5V8c-1.4-.5-3.3-.5-4.7.5Z" fill="white" />
        </svg>
        @break
    @case('google-books')
        <svg viewBox="0 0 24 24" fill="none" {{ $attributes->class($class) }}>
            <circle cx="12" cy="12" r="9.5" fill="#4285F4" />
            <path d="M8 6.5h8V19l-4-2.5-4 2.5V6.5Z" fill="white" />
        </svg>
        @break
    @case('plesk')
        <svg viewBox="0 0 24 24" fill="none" {{ $attributes->class($class) }}>
            <rect x="3" y="4" width="18" height="4" rx="1" fill="#1565C0" />
            <rect x="3" y="10" width="18" height="4" rx="1" fill="#1565C0" />
            <rect x="3" y="16" width="18" height="4" rx="1" fill="#1565C0" />
            <circle cx="18.5" cy="6" r="1" fill="rgba(255,255,255,0.65)" />
            <circle cx="18.5" cy="12" r="1" fill="rgba(255,255,255,0.65)" />
        </svg>
        @break
    @default
        <svg viewBox="0 0 24 24" {{ $attributes->class($class) }}>
            <circle cx="12" cy="12" r="9.5" fill="#999" />
        </svg>
@endswitch
