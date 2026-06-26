@props(['title', 'buttonText', 'link' => '#'])
<div class="bg-frost-dark text-white p-fluid-md text-center flex flex-col items-center justify-center my-8">
    <h3 class="text-2xl md:text-4xl font-bold tracking-tight text-white mb-4">{{ $title }}</h3>
    <p class="text-sm text-neutral-400 max-w-md mb-6">{{ $slot }}</p>
    <a href="{{ $link }}" class="bg-white text-frost-dark font-semibold text-xs px-6 py-3 hover:bg-neutral-100 transition inline-block">
        {{ $buttonText }}
    </a>
</div>