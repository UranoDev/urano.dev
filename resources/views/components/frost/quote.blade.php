@props(['author' => null])
<div class="border-l-2 border-frost-dark pl-6 my-8 italic">
    <blockquote class="text-lg md:text-xl text-frost-dark leading-relaxed font-light">
        "{{ $slot }}"
    </blockquote>
    @if($author)
        <cite class="block text-xs font-semibold not-italic text-frost-muted uppercase tracking-wider mt-3">— {{ $author }}</cite>
    @endif
</div>