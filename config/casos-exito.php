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
        'acciones' => [
            ['numero' => '01', 'accion' => 'Poner el enlace de contacto en la biografía de Instagram', 'estado' => 'Habilitada', 'nota' => 'Ya hay a dónde enlazar: el sitio y su página de precios. Falta pegarlo en el perfil.'],
            ['numero' => '02', 'accion' => 'Cambiar la biografía para que nombre todo lo que se hace', 'estado' => 'Pendiente', 'nota' => 'Sigue diciendo solo «tenis». Botas, bolsas y mochilas siguen invisibles.'],
            ['numero' => '03', 'accion' => 'Abrir el perfil de empresa en Google', 'estado' => 'Pendiente', 'nota' => 'Sigue siendo la acción de mayor rendimiento de toda la lista.'],
            ['numero' => '04', 'accion' => 'Pedir reseña a los clientes anteriores', 'estado' => 'Pendiente', 'nota' => 'Cero reseñas. El sitio tiene la sección de testimonios lista y vacía.'],
            ['numero' => '05', 'accion' => 'Cerrar los precios pendientes', 'estado' => 'Hecha', 'nota' => 'La limpieza infantil quedó en $100 y la entrega express como +$100 sobre el servicio, no en lugar de él.'],
            ['numero' => '06', 'accion' => 'Fijar un formato de foto y no moverlo', 'estado' => 'Pendiente', 'nota' => 'Ahora pesa más: la galería compara el antes y el después lado a lado, y ahí se nota si la luz cambia.'],
            ['numero' => '07', 'accion' => 'Publicar tres veces por semana', 'estado' => 'Pendiente', 'nota' => 'La constancia es lo que ningún sitio sustituye.'],
            ['numero' => '08', 'accion' => 'Empezar con video corto', 'estado' => 'Pendiente', 'nota' => 'El perfil sigue sin ninguno.'],
            ['numero' => '09', 'accion' => 'Completar las historias destacadas', 'estado' => 'Habilitada', 'nota' => 'La de precios ya no necesita captura de pantalla: hay una dirección propia que se manda por mensaje.'],
            ['numero' => '10', 'accion' => 'Bajar el tamaño de la marca de agua', 'estado' => 'Pendiente', 'nota' => 'Sin cambios.'],
            ['numero' => '11', 'accion' => 'Convertir Facebook en ficha de negocio', 'estado' => 'Pendiente', 'nota' => 'Ya existen los datos que le faltan —dirección, horarios, teléfono—: solo hay que copiarlos.'],
            ['numero' => '12', 'accion' => 'Publicar el sitio', 'estado' => 'Hecha', 'nota' => 'En línea en calzaclean.com.'],
            ['numero' => '13', 'accion' => 'Armar paquetes', 'estado' => 'Decidida', 'nota' => 'Por ahora no hay. El sitio no los menciona, ni siquiera como «próximamente».'],
            ['numero' => '14', 'accion' => 'Ofrecer recolección a domicilio', 'estado' => 'Hecha', 'nota' => 'Dos zonas con su costo, publicadas y editables desde el panel.'],
            ['numero' => '15', 'accion' => 'Buscar alianzas locales', 'estado' => 'Pendiente', 'nota' => 'Sin cambios.'],
        ],

        /*
        | Capturas del sitio entregado. Cada entrada lleva 'imagen' —una ruta
        | pública— y 'pie'. Mientras la lista esté vacía la sección no se
        | dibuja: es preferible a un hueco con texto de relleno.
        */
        'capturas' => [],
    ],

];
