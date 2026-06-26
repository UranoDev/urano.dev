<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'slug' => 'saas-reservas-tours',
                'title' => 'SaaS de Reservas y Tours',
                'category' => 'PRODUCTO SAAS',
                'meta_title' => 'SaaS de Reservaciones y Tours | Urano Dev',
                'hero_title' => 'Tu motor de reservas.<br>Sin intermediarios.',
                'hero_desc' => 'Un software diseñado especialmente para tour operadores, experiencias locales y hoteles boutique. Toma el control de tus fechas, cupos y cobros de forma automatizada y profesional.',
                'cta_text' => 'Solicitar Demo Gratis',
                'benefits_title' => '¿Por qué elegir nuestro motor de reservas?',
                'benefits_subtitle' => 'Elimina la carga administrativa y duplica tus ventas directas en Tequisquiapan y todo México.',
                'benefits' => [
                    [
                        'title' => 'Control de Inventario y Cupos',
                        'desc' => 'Olvídate del sobrecupo. Nuestro calendario inteligente bloquea fechas y horarios en tiempo real para todas tus actividades.',
                    ],
                    [
                        'title' => 'Pagos y CFDI Integrados',
                        'desc' => 'Acepta tarjetas de crédito, débito y transferencias, y emite facturas CFDI 4.0 automáticamente al confirmarse la reserva.',
                    ],
                    [
                        'title' => 'Soporte y WhatsApp Local',
                        'desc' => 'Estamos en Tequisquiapan. Te ayudamos a configurar notificaciones automáticas de confirmación y ubicación por WhatsApp.',
                    ],
                ],
                'quote' => 'El turismo en México crece cuando los negocios locales se liberan de la administración manual y se enfocan en la experiencia del huésped.',
                'quote_author' => 'Urano Dev — Filosofía de Software',
                'modules_title' => 'Módulos incluidos en el sistema',
                'modules' => [
                    'Calendario interactivo para tu personal',
                    'Motor de reserva incrustable en tu web actual',
                    'Pasarela de cobro vía Stripe o Mercado Pago',
                    'Generación instantánea de tickets QR',
                    'Panel de reportes de ventas y ocupación',
                    'Recordatorios automáticos por WhatsApp',
                ],
                'cta_title' => '¿Listo para digitalizar tus tours y experiencias?',
                'cta_desc' => 'Conecta tu calendario, automatiza tus cobros y enfócate en lo que mejor sabes hacer: dar una experiencia de viaje inolvidable.',
            ],
            [
                'slug' => 'facturacion-cfdi',
                'title' => 'Facturación CFDI 4.0',
                'category' => 'INTEGRACIÓN / CFDI',
                'meta_title' => 'Generación de CFDI 4.0 Automatizada | Urano Dev',
                'hero_title' => 'Facturación integrada.<br>Sin dolor de cabeza.',
                'hero_desc' => 'Automatiza la emisión de CFDI 4.0 para tus clientes de forma transparente. Conectamos tus sistemas de ventas o web actual con los principales PACs de México.',
                'cta_text' => 'Solicitar Integración',
                'benefits_title' => 'Solución de Facturación CFDI',
                'benefits_subtitle' => 'Cumple con el SAT sin interrumpir tus ventas ni tus flujos de caja.',
                'benefits' => [
                    [
                        'title' => 'Emisión 100% Automatizada',
                        'desc' => 'Emite facturas al momento de la venta o permite que tus clientes se autofacturen a través de un portal amigable.',
                    ],
                    [
                        'title' => 'Integración vía API o Webhook',
                        'desc' => 'Conectamos tu ERP, tienda Shopify, WooCommerce o desarrollo a la medida con servicios de timbrado fiscal en milisegundos.',
                    ],
                    [
                        'title' => 'Soporte ante cambios de Ley',
                        'desc' => 'Olvídate de las actualizaciones fiscales. Mantenemos tu sistema al día con las directrices del SAT sin que muevas un dedo.',
                    ],
                ],
                'quote' => 'La automatización de procesos fiscales no es solo un requisito legal; es la liberación de horas de trabajo para tu equipo administrativo.',
                'quote_author' => 'Urano Dev — Soluciones de Cobro',
                'modules_title' => 'Qué incluye el servicio de CFDI',
                'modules' => [
                    'Conexión con PACs autorizados en México',
                    'Portal de autofacturación para tus clientes',
                    'Generación de archivos XML y PDF personalizados',
                    'Validación de RFC y datos fiscales en tiempo real',
                    'Cancelaciones y notas de crédito automatizadas',
                    'Soporte para CFDI con complementos específicos',
                ],
                'cta_title' => '¿Quieres automatizar tu facturación fiscal?',
                'cta_desc' => 'Integra hoy mismo la emisión de CFDI 4.0 en tu plataforma y despídete de la carga administrativa de fin de mes.',
            ],
            [
                'slug' => 'procesamiento-pagos',
                'title' => 'Procesamiento de Pagos',
                'category' => 'PASARELAS DE PAGO',
                'meta_title' => 'Procesamiento de Pagos Seguro para PYMEs | Urano Dev',
                'hero_title' => 'Acepta pagos en línea.<br>Vende mientras duermes.',
                'hero_desc' => 'Implementamos pasarelas de pago seguras para tu negocio local o turístico. Acepta tarjetas, transferencias y pagos en efectivo con la máxima tasa de aprobación.',
                'cta_text' => 'Integrar Pasarela',
                'benefits_title' => 'Ventajas de integrar pagos profesionales',
                'benefits_subtitle' => 'Haz que pagar sea la parte más fácil del viaje de tu cliente.',
                'benefits' => [
                    [
                        'title' => 'Múltiples Métodos de Pago',
                        'desc' => 'Permite pagos con Tarjetas (Visa, MasterCard, Amex), transferencias SPEI directas y depósitos en tiendas OXXO.',
                    ],
                    [
                        'title' => 'Protección Antifraude',
                        'desc' => 'Implementamos reglas avanzadas de prevención de fraudes y soporte para autenticación 3D Secure para transacciones seguras.',
                    ],
                    [
                        'title' => 'Conciliación de Cuentas',
                        'desc' => 'Sincroniza tus ingresos recibidos directamente con tu sistema administrativo, automatizando las confirmaciones de compra.',
                    ],
                ],
                'quote' => 'Una experiencia de pago fluida y segura puede incrementar tu tasa de conversión digital en más de un 25%.',
                'quote_author' => 'Urano Dev — Conversión y Pagos',
                'modules_title' => 'Características del sistema de pagos',
                'modules' => [
                    'Integración de Stripe, Mercado Pago o Conekta',
                    'Soporte para cobros únicos o suscripciones (SaaS)',
                    'Formularios de pago embebidos e higiénicos',
                    'Links de pago para envío rápido por chat',
                    'Soporte para cobro multi-divisa (MXN / USD)',
                    'Sincronización con webhooks para avisos inmediatos',
                ],
                'cta_title' => '¿Listo para vender en línea de forma segura?',
                'cta_desc' => 'Hagamos que tus clientes puedan reservar y pagar en segundos con absoluta confianza y seguridad.',
            ],
            [
                'slug' => 'sincronizacion-otas',
                'title' => 'Sincronización con OTAs',
                'category' => 'CANALES / INTEGRACIÓN',
                'meta_title' => 'Sincronización de Calendarios con OTAs | Urano Dev',
                'hero_title' => 'Sincroniza tus canales.<br>Evita la sobreventa.',
                'hero_desc' => 'Conectamos tu inventario y calendario interno de reservas con plataformas globales como Airbnb, Booking, Expedia y TripAdvisor en tiempo real.',
                'cta_text' => 'Conectar Canales',
                'benefits_title' => 'Conectividad OTA en tiempo real',
                'benefits_subtitle' => 'Mantén tus calendarios sincronizados al segundo en todos los canales.',
                'benefits' => [
                    [
                        'title' => 'Actualización de Calendarios',
                        'desc' => 'Cuando entra una reserva en Booking o Airbnb, nuestro sistema bloquea esa fecha automáticamente en todos tus demás canales.',
                    ],
                    [
                        'title' => 'Control Centralizado de Precios',
                        'desc' => 'Cambia tus tarifas de temporada desde un solo panel y propágalas instantáneamente a todos tus puntos de venta digitales.',
                    ],
                    [
                        'title' => 'Soporte Técnico de Integración',
                        'desc' => 'Configuramos las conexiones API y el formato iCal de forma segura para garantizar cero fallos de comunicación.',
                    ],
                ],
                'quote' => 'La presencia omnicanal sin automatización es una receta para el caos administrativo. La sincronización en tiempo real es paz mental.',
                'quote_author' => 'Urano Dev — Integración de Canales',
                'modules_title' => 'Módulos de sincronización OTA',
                'modules' => [
                    'Conexión con Channel Managers líderes',
                    'Soporte bidireccional para Airbnb y Booking.com',
                    'Tarifas dinámicas y reglas de temporada',
                    'Bloqueo inteligente de fechas por mantenimiento',
                    'Importación y exportación de calendarios en tiempo real',
                    'Logs de sincronización y alertas de error',
                ],
                'cta_title' => '¿Quieres expandir tus canales de venta?',
                'cta_desc' => 'Duplica tu visibilidad en internet y duerme tranquilo sabiendo que tus calendarios están perfectamente sincronizados.',
            ],
            [
                'slug' => 'mensajeria-whatsapp',
                'title' => 'Mensajería por WhatsApp',
                'category' => 'NOTIFICACIONES / AUTOMATIZACIÓN',
                'meta_title' => 'Automatización de Notificaciones por WhatsApp | Urano Dev',
                'hero_title' => 'Comunícate por WhatsApp.<br>De forma automática.',
                'hero_desc' => 'Envía confirmaciones de reservas, recordatorios de citas, ligas de pago y ubicaciones directamente al WhatsApp de tus clientes sin escribir un solo mensaje manual.',
                'cta_text' => 'Automatizar Mensajes',
                'benefits_title' => 'Notificaciones de alta conversión',
                'benefits_subtitle' => 'Una comunicación inmediata y profesional incrementa la satisfacción del cliente en un 40%.',
                'benefits' => [
                    [
                        'title' => 'Confirmaciones y Tickets',
                        'desc' => 'Tus clientes reciben un mensaje de bienvenida formal con su itinerario, código QR de acceso y liga de facturación al instante.',
                    ],
                    [
                        'title' => 'Recordatorios y Ubicaciones',
                        'desc' => 'Envía recordatorios automáticos 24 horas antes del tour con instrucciones de llegada, clima local y la ubicación exacta de Google Maps.',
                    ],
                    [
                        'title' => 'Encuestas de Satisfacción',
                        'desc' => 'Automatiza el seguimiento post-evento para recopilar comentarios y dirigir a tus clientes satisfechos a dejar reseñas en TripAdvisor o Google.',
                    ],
                ],
                'quote' => 'El mejor servicio al cliente no es el que responde rápido, sino el que se anticipa a las dudas del viajero mediante notificaciones inteligentes.',
                'quote_author' => 'Urano Dev — Automatización de Mensajería',
                'modules_title' => 'Características del módulo de WhatsApp',
                'modules' => [
                    'Integración oficial con WhatsApp Business API',
                    'Plantillas de mensajes aprobadas por Meta',
                    'Envío automático de PDFs de tickets y facturas',
                    'Envío de geolocalización activa para tours',
                    'Reglas de activación basadas en eventos de reserva',
                    'Reportes de entrega y lectura de mensajes',
                ],
                'cta_title' => '¿Quieres mejorar tu experiencia de cliente?',
                'cta_desc' => 'Automatiza tus mensajes de WhatsApp y mantén informados a tus viajeros en cada paso del camino de manera profesional.',
            ],
            [
                'slug' => 'ecommerce-sitios-turisticos',
                'title' => 'eCommerce y Sitios Turísticos',
                'category' => 'DISEÑO WEB / ECOMMERCE',
                'meta_title' => 'eCommerce y Páginas Web Turísticas | Urano Dev',
                'hero_title' => 'Tu sitio web turístico.<br>Diseñado para convertir.',
                'hero_desc' => 'Desarrollamos páginas web de alto rendimiento y tiendas en línea con motores de reserva integrados, optimizadas para destacar en Google y vender de forma directa.',
                'cta_text' => 'Crear mi Sitio',
                'benefits_title' => 'Diseño web enfocado en conversiones',
                'benefits_subtitle' => 'No diseñamos páginas bonitas; construimos activos digitales de venta directa.',
                'benefits' => [
                    [
                        'title' => 'Velocidad y SEO Superior',
                        'desc' => 'Sitios ultrarrápidos que cargan en menos de un segundo, listos para posicionarse orgánicamente en los resultados locales de Google.',
                    ],
                    [
                        'title' => 'Experiencia de Reserva Fluida',
                        'desc' => 'Diseño enfocado en dispositivos móviles que permite al viajero reservar y pagar su experiencia en menos de tres clics.',
                    ],
                    [
                        'title' => 'Administración Autónoma',
                        'desc' => 'Un gestor de contenidos amigable para que puedas actualizar fotos, descripciones, precios y promociones tú mismo con facilidad.',
                    ],
                ],
                'quote' => 'Tu sitio web es el escaparate digital de tu negocio. Si no permite reservas directas y cobros rápidos, estás regalando tus comisiones.',
                'quote_author' => 'Urano Dev — Diseño de Conversión',
                'modules_title' => 'Módulos web incluidos',
                'modules' => [
                    'Maquetación responsive optimizada para móviles',
                    'Estructura SEO local para Tequisquiapan y México',
                    'Motor de reservas nativo embebido',
                    'Galerías dinámicas de fotos y videos',
                    'Integración de reseñas de TripAdvisor y Google',
                    'Formularios de contacto inteligentes con geolocalización',
                ],
                'cta_title' => '¿Listo para lanzar tu nueva web turística?',
                'cta_desc' => 'Comienza a recibir reservas directas hoy mismo con una página web moderna, profesional y optimizada para tu negocio.',
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(['slug' => $service['slug']], $service);
        }
    }
}
