@props(['title'])
<div class="border border-frost-border p-6 bg-white hover:border-frost-dark transition-all duration-300">
    <h4 class="text-lg font-semibold mb-2 tracking-tight">{{ $title }}</h4>
    <p class="text-sm text-frost-muted leading-relaxed">{{ $slot }}</p>
</div>