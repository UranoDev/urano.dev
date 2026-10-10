<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        /*
         * Lo que ven LinkedIn, WhatsApp y los buscadores al compartir la página.
         * Cada vista puede mandar los slots title, description, image y url; lo
         * que no mande cae en los datos del sitio. Los artículos del blog se
         * generan fuera de una petición web, así que mandan su url.
         */
        $limpio = fn ($valor) => $valor instanceof \Illuminate\Contracts\Support\Htmlable
            ? trim(preg_replace('/\s+/', ' ', $valor->toHtml()))
            : e(trim(preg_replace('/\s+/', ' ', (string) $valor)));

        $pageTitle = new \Illuminate\Support\HtmlString($limpio($title ?? '') ?: 'Urano Dev');
        $pageDescription = new \Illuminate\Support\HtmlString($limpio($description ?? '')
            ?: 'Software a la medida para PYMEs y empresas turísticas: reservaciones, facturación CFDI 4.0, pagos en línea e integraciones con los sistemas que ya usas.');
        $pageImage = new \Illuminate\Support\HtmlString($limpio($image ?? '') ?: asset('images/og/urano-dev.png'));
        $pageUrl = new \Illuminate\Support\HtmlString($limpio($url ?? '') ?: url()->current());
        $pageAuthor = new \Illuminate\Support\HtmlString($limpio($author ?? '') ?: 'Urano Gonzalez');
        // Los artículos mandan su fecha de publicación; con ella la página se
        // declara como artículo.
        $pagePublished = $limpio($published ?? '');
    @endphp
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="author" content="{{ $pageAuthor }}">
    <link rel="canonical" href="{{ $pageUrl }}">
    @if ($pagePublished)
        <meta property="og:type" content="article">
        <meta property="article:published_time" content="{{ new \Illuminate\Support\HtmlString($pagePublished) }}">
        <meta property="article:author" content="{{ $pageAuthor }}">
    @else
        <meta property="og:type" content="website">
    @endif
    <meta property="og:site_name" content="Urano Dev">
    <meta property="og:locale" content="es_MX">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $pageUrl }}">
    <meta property="og:image" content="{{ $pageImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex flex-col min-h-screen">

<header x-data="{ mobileMenuOpen: false }" class="border-b border-frost-border sticky top-0 bg-white/80 backdrop-blur z-50">
    <div class="max-w-6xl mx-auto px-fluid-sm h-20 flex items-center justify-between">
        <a href="/" aria-label="Urano Dev" class="block">
            <svg viewBox="0 0 390 100" class="h-8 w-auto" role="img" aria-hidden="true">
                <style>
                    text { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; letter-spacing: 0.02em; }
                </style>
                <g transform="translate(10,10) scale(0.8)">
                    <circle cx="50" cy="50" r="36" stroke="#111111" stroke-width="3.5" stroke-dasharray="7 6" fill="none" />
                    <circle cx="50" cy="86" r="7" fill="#111111" />
                    <circle cx="18.8" cy="32" r="7" fill="#111111" />
                    <circle cx="81.2" cy="32" r="10.5" fill="#FAFAFA" />
                    <circle cx="81.2" cy="32" r="8" stroke="#213A9A" stroke-width="5" fill="none" />
                </g>
                <text x="112" y="62" font-size="42">
                    <tspan font-weight="700" fill="#111111">URANO</tspan>
                    <tspan font-weight="400" fill="#666666" dx="10">DEV</tspan>
                </text>
            </svg>
        </a>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center space-x-8 text-sm font-medium">
            <a href="/" class="hover:text-frost-muted transition">Inicio</a>
            <a href="{{ route('blog.index') }}" class="hover:text-frost-muted transition">Blog</a>
            <a href="/nosotros" class="hover:text-frost-muted transition">Nosotros</a>
            <a href="/links" class="hover:text-frost-muted transition">Links</a>
            <a href="{{ route('contact') }}" class="hover:text-frost-muted transition">Contacto</a>
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
        <a href="{{ route('contact') }}" class="block text-base font-medium text-frost-dark hover:text-frost-muted transition">Contacto</a>

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
            <h2 class="text-xs font-bold uppercase tracking-wider text-frost-muted mb-3">Navegación</h2>
            <ul class="space-y-2 text-sm">
                <li><a href="/" class="hover:underline">Inicio</a></li>
                <li><a href="{{ route('services.index') }}" class="hover:underline">Servicios</a></li>
                <li><a href="{{ route('casos-exito.index') }}" class="hover:underline">Casos de éxito</a></li>
                <li><a href="{{ route('blog.index') }}" class="hover:underline">Blog</a></li>
                <li><a href="/nosotros" class="hover:underline">Nosotros</a></li>
                <li><a href="{{ route('contact') }}" class="hover:underline">Contacto</a></li>
            </ul>
        </div>
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-frost-muted mb-3">Conectar</h2>            <div class="flex space-x-4">
                <a href="https://www.linkedin.com/in/uranogonzalez" aria-label="Urano González en LinkedIn" target="_blank" rel="noopener" class="text-frost-dark hover:text-frost-muted"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 1 1 0-4.125 2.062 2.062 0 0 1 0 4.125zM7.119 20.452H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>
                <a href="https://www.youtube.com/@uranodev" aria-label="Urano Dev en YouTube" target="_blank" rel="noopener" class="text-frost-dark hover:text-frost-muted"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></a>
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