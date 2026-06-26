<x-layouts.app>
    <x-slot:title>Nosotros — Urano Dev</x-slot:title>

    <section class="max-w-4xl mx-auto px-fluid-sm py-fluid-md">
        <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Quiénes somos</span>
        <h1 class="text-3xl md:text-5xl font-bold tracking-tight mt-2 mb-6">
            Tecnología hecha a la medida,<br>desde Tequisquiapan.
        </h1>

        <p class="text-base text-frost-dark mb-6 leading-relaxed max-w-2xl">
            Somos un equipo de desarrollo de software especializado en soluciones para PYMEs y empresas del sector turístico en México.
            Construimos sistemas de reservaciones, facturación CFDI, procesamiento de pagos e integración con canales de distribución.
        </p>

        <x-frost.quote author="Urano Dev">
            Creemos que la tecnología bien implementada no complica tu negocio, lo libera.
        </x-frost.quote>

        <p class="text-base text-frost-dark mt-6 mb-12 leading-relaxed max-w-2xl">
            Desde el Pueblo Mágico de Tequisquiapan, Querétaro, trabajamos con empresas de todo México para automatizar sus operaciones,
            reducir errores manuales y aumentar sus ingresos a través de soluciones robustas en PHP y Laravel.
        </p>

        @if($team->isNotEmpty())
            <div class="border-t border-frost-border pt-12">
                <h2 class="text-xl font-bold tracking-tight mb-8">Equipo</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                    @foreach($team as $member)
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                @if($member->avatar)
                                    <img src="{{ asset('storage/' . $member->avatar) }}"
                                         alt="{{ $member->name }}"
                                         class="w-16 h-16 rounded-full object-cover bg-frost-light">
                                @else
                                    <div class="w-16 h-16 bg-frost-dark rounded-full flex items-center justify-center text-white text-lg font-bold">
                                        {{ $member->initials() }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-sm">{{ $member->name }}</h4>
                                <p class="text-xs text-frost-muted mb-1">
                                    {{ $member->role === \App\Enums\Role::Admin ? 'Administrador' : 'Autor' }}
                                </p>
                                @if($member->bio)
                                    <p class="text-xs text-frost-dark leading-relaxed">{{ $member->bio }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    <section class="max-w-4xl mx-auto px-fluid-sm py-8">
        <x-frost.cta
            title="¿Tienes un proyecto en mente?"
            buttonText="Hablemos"
            :link="'https://wa.me/' . config('services.whatsapp.number') . '?text=' . urlencode('Hola, quiero hablar sobre un proyecto')">
            Cuéntanos qué necesitas y te damos una propuesta sin compromiso.
        </x-frost.cta>
    </section>

</x-layouts.app>
