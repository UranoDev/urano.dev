<x-layouts.app>
    <x-slot:title>
        Urano Dev | Tecnología y Soluciones de Software para PYMEs y Turismo
    </x-slot:title>
    <section class="max-w-6xl mx-auto px-fluid-sm py-fluid-lg text-center md:text-left">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
            <div class="md:col-span-8">
                <h1 class="text-4xl md:text-7xl font-bold tracking-tight mb-6 leading-[1.05]">
                    Reservaciones.<br>
                    Facturación.<br>
                    Pagos.
                </h1>

                <p class="text-lg md:text-xl text-frost-muted max-w-xl mb-8 leading-relaxed">
                    Desarrollamos soluciones tecnológicas a la medida para PYMEs y empresas turísticas.
                    Automatiza tus cobros, genera facturas CFDI y gestiona tus reservaciones de manera profesional en PHP y Laravel.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    <a href="https://wa.me/{{ config('services.whatsapp.number') }}?text={{ urlencode('quiero saber más de ' . request()->url()) }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="bg-frost-dark text-white font-semibold text-sm px-6 py-3 hover:bg-opacity-90 transition text-center">
                        Solicitar Cotización
                    </a>

                    <a href="{{ route('services.index') }}"
                       class="border border-frost-border font-semibold text-sm px-6 py-3 hover:border-frost-dark transition text-center">
                        Ver Servicios →
                    </a>                </div>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-fluid-sm py-8">
        <div class="border-t border-frost-border pt-12">

            <h2 class="text-2xl font-bold tracking-tight mb-2">
                Servicios de Tecnología para tu Negocio
            </h2>

            <p class="text-sm text-frost-muted mb-8">
                Especializados en digitalizar operaciones y maximizar los ingresos del sector turístico y empresarial.
            </p>

            <x-frost.features>

                <x-frost.feature-box title="SaaS de Reservaciones y Tours">
                    Sistemas para gestionar reservaciones en tiempo real, controlar cupos, asignar guías y programar salidas para tours, hoteles boutique o experiencias locales.
                    <a href="/servicios/saas-reservas-tours" class="hover:underline text-frost-dark font-semibold block mt-4 text-xs tracking-tight">Saber más →</a>
                </x-frost.feature-box>

                <x-frost.feature-box title="Facturación CFDI 4.0">
                    Generación automatizada de facturación electrónica CFDI directamente tras la compra o reserva, integrada de manera transparente con PACs autorizados.
                    <a href="/servicios/facturacion-cfdi" class="hover:underline text-frost-dark font-semibold block mt-4 text-xs tracking-tight">Saber más →</a>
                </x-frost.feature-box>

                <x-frost.feature-box title="Procesamiento de Pagos">
                    Integración segura de pasarelas de pago como Stripe, Conekta o Mercado Pago para aceptar tarjetas de crédito/débito, transferencias y depósitos.
                    <a href="/servicios/procesamiento-pagos" class="hover:underline text-frost-dark font-semibold block mt-4 text-xs tracking-tight">Saber más →</a>
                </x-frost.feature-box>

                <x-frost.feature-box title="Sincronización con OTAs">
                    Conecta tu inventario o agenda en tiempo real con canales de distribución externos como Airbnb, Booking, Expedia y TripAdvisor para evitar sobreventas.
                    <a href="/servicios/sincronizacion-otas" class="hover:underline text-frost-dark font-semibold block mt-4 text-xs tracking-tight">Saber más →</a>
                </x-frost.feature-box>

                <x-frost.feature-box title="Mensajería por WhatsApp">
                    Envío automatizado de confirmaciones de reserva, recordatorios de citas, tickets digitales y encuestas de satisfacción directamente al WhatsApp del cliente.
                    <a href="/servicios/mensajeria-whatsapp" class="hover:underline text-frost-dark font-semibold block mt-4 text-xs tracking-tight">Saber más →</a>
                </x-frost.feature-box>

                <x-frost.feature-box title="eCommerce y Sitios Turísticos">
                    Sitios web modernos con motores de reservación integrados, optimizados para posicionamiento en Google y diseñados para convertir visitas en clientes reales.
                    <a href="/servicios/ecommerce-sitios-turisticos" class="hover:underline text-frost-dark font-semibold block mt-4 text-xs tracking-tight">Saber más →</a>
                </x-frost.feature-box>

            </x-frost.features>

        </div>
    </section>

    <section class="max-w-6xl mx-auto px-fluid-sm py-12 border-t border-frost-border">
        <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Tequisquiapan, Querétaro</span>
        <h2 class="text-3xl font-bold tracking-tight mt-2 mb-4">
            Desarrollo local con impacto global
        </h2>
        <p class="text-base text-frost-dark mb-4 leading-relaxed max-w-3xl">
            Trabajamos desde el Pueblo Mágico de Tequisquiapan, diseñando e implementando soluciones tecnológicas robustas para el sector turístico.
        </p>
        <p class="text-base text-frost-dark mb-0 leading-relaxed max-w-3xl">
            Nuestro objetivo es que tu empresa crezca automatizando sus procesos de venta, cobro y atención al cliente.
        </p>
    </section>

    <section class="max-w-6xl mx-auto px-fluid-sm py-8">
        <x-frost.cta
            title="¿Listo para automatizar las reservaciones y operaciones de tu negocio?"
            buttonText="Hablemos de tu proyecto"
            :link="'https://wa.me/' . config('services.whatsapp.number') . '?text=' . urlencode('quiero saber más de ' . request()->url())">

            Ayudamos a PYMEs y empresas turísticas en Tequisquiapan y todo México a conectar reservaciones, cobros y facturación mediante soluciones escalables y seguras.

        </x-frost.cta>
    </section>

</x-layouts.app>