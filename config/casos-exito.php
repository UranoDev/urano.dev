<?php

/*
|--------------------------------------------------------------------------
| Casos de éxito
|--------------------------------------------------------------------------
|
| El registro de los proyectos entregados que tienen caso publicado. La clave
| de cada entrada es el slug de su dirección: /casos-exito/{proyecto}. Solo
| entran proyectos reales, en producción y con permiso del cliente.
|
| El texto largo de cada caso vive en su vista, resources/views/casos-exito/
| {vista}.blade.php. Aquí queda lo que necesitan el índice, el sitemap y la
| ficha de la página.
|
*/

return [

    'calzaclean' => [
        'nombre' => 'CalzaClean',
        'vista' => 'calzaclean',
        'giro' => 'Limpieza y restauración de tenis a mano',
        'lugar' => 'San Juan del Río, Querétaro',
        'entregado' => 'Septiembre de 2026',
        'sitio' => 'https://calzaclean.com',
        'meta_title' => 'CalzaClean, caso de éxito | Urano Dev',
        'resumen' => 'Un taller de una sola persona, con 50 seguidores y cero reseñas, '
            .'pasó de mandar capturas de pantalla por WhatsApp a tener sitio, lista de '
            .'precios con dirección propia y un panel de cuatro pantallas.',

        /*
        | Las quince acciones del diagnóstico de marca, con el estado que les
        | dio la revisión del 7 de septiembre de 2026. Nueve son de operación
        | y ninguna se resuelve construyendo software.
        */

        /*
        | Capturas del sitio entregado. Cada entrada lleva 'imagen' —una ruta
        | pública— y 'pie'. Mientras la lista esté vacía la sección no se
        | dibuja: es preferible a un hueco con texto de relleno.
        */
        'capturas' => [],
    ],

];
