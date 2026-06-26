<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Frost Laravel Theme' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex flex-col min-h-screen">

<header x-data="{ mobileMenuOpen: false }" class="border-b border-frost-border sticky top-0 bg-white/80 backdrop-blur z-50">
    <div class="max-w-6xl mx-auto px-fluid-sm h-20 flex items-center justify-between">
        <a href="/" class="text-xl font-bold tracking-tighter text-frost-dark">Urano Dev<span class="text-frost-muted">.</span></a>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center space-x-8 text-sm font-medium">
            <a href="/" class="hover:text-frost-muted transition">Inicio</a>
            <a href="{{ route('blog.index') }}" class="hover:text-frost-muted transition">Blog</a>
            <a href="/nosotros" class="hover:text-frost-muted transition">Nosotros</a>
            <a href="/links" class="hover:text-frost-muted transition">Links</a>
        </nav>

        <div class="flex items-center gap-4">
            <!-- Desktop Auth Buttons -->
            <div class="hidden md:flex items-center gap-4">
                @if ($isStatic ?? false)
                    <a href="{{ route('login') }}" class="text-sm text-frost-muted hover:text-frost-dark transition">Acceder</a>
                @else
                    @auth
                        @if(auth()->user()->isAdmin() || auth()->user()->isAuthor())
                            <a href="{{ route('dashboard') }}" class="text-sm font-medium hover:text-frost-muted transition">Dashboard</a>
                        @else
                            <span class="text-sm text-frost-muted">{{ auth()->user()->name }}</span>
                        @endif

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm font-medium hover:text-frost-muted transition">
                                Cerrar sesión
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-frost-muted hover:text-frost-dark transition">Acceder</a>
                    @endauth
                @endif
            </div>

            <!-- Hamburger Button (Mobile Only) -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-frost-dark hover:text-frost-muted focus:outline-none" aria-label="Toggle menu">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display: none;" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation Panel -->
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="md:hidden border-t border-frost-border bg-white px-fluid-sm py-4 space-y-3"
         style="display: none;">
        <a href="/" class="block text-base font-medium text-frost-dark hover:text-frost-muted transition">Inicio</a>
        <a href="{{ route('blog.index') }}" class="block text-base font-medium text-frost-dark hover:text-frost-muted transition">Blog</a>
        <a href="/nosotros" class="block text-base font-medium text-frost-dark hover:text-frost-muted transition">Nosotros</a>
        <a href="/links" class="block text-base font-medium text-frost-dark hover:text-frost-muted transition">Links</a>

        <div class="pt-4 border-t border-frost-border flex flex-col gap-3">
            @if ($isStatic ?? false)
                <a href="{{ route('login') }}" class="block text-base font-medium text-frost-dark hover:text-frost-muted transition">Acceder</a>
            @else
                @auth
                    @if(auth()->user()->isAdmin() || auth()->user()->isAuthor())
                        <a href="{{ route('dashboard') }}" class="block text-base font-medium text-frost-dark hover:text-frost-muted transition">Dashboard</a>
                    @else
                        <span class="block text-base text-frost-muted">{{ auth()->user()->name }}</span>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="block w-full text-left text-base font-medium text-frost-dark hover:text-frost-muted transition">
                            Cerrar sesión
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block text-base font-medium text-frost-dark hover:text-frost-muted transition">Acceder</a>
                @endauth
            @endif        </div>
    </div>
</header>

<main class="flex-grow">
    {{ $slot }}
</main>

<footer class="border-t border-frost-border bg-frost-light py-fluid-md">
    <div class="max-w-6xl mx-auto px-fluid-sm grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="md:col-span-2">
            <span class="text-lg font-bold tracking-tighter">Urano Dev</span>
        </div>
        <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-frost-muted mb-3">Navegación</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="/" class="hover:underline">Inicio</a></li>
                <li><a href="{{ route('blog.index') }}" class="hover:underline">Blog</a></li>
                <li><a href="/nosotros" class="hover:underline">Nosotros</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-frost-muted mb-3">Conectar</h4>            <div class="flex space-x-4">
                <a href="#" class="text-frost-dark hover:text-frost-muted"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg></a>
                <a href="#" class="text-frost-dark hover:text-frost-muted"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg></a>
            </div>
        </div>
    </div>
    <div class="max-w-6xl mx-auto px-fluid-sm mt-8 pt-4 border-t border-frost-border flex flex-col md:flex-row justify-between text-xs text-frost-muted">
        <p>&copy; 2016 - {{ date('Y') }} Urano Dev.</p>
        <p>Tecnología para turismo y PYMEs en crecimiento.</p>
    </div>
</footer>

<x-whatsapp-button :url="$whatsappUrl ?? null" />

@livewireScripts
</body>
</html>