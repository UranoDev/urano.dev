# Tareas del proyecto

## Prioridad 1 — Fundacion

| Status | Tarea                                                                             | Git label                |
|--------|-----------------------------------------------------------------------------------|--------------------------|
| [x]    | Estructura base: migraciones de usuarios y configuracion inicial                  | `chore: setup`           |
| [x]    | Autenticacion: registro, login, logout, recuperar password, verificacion de email | `feat: auth`             |
| [x]    | Registro e inicio de sesion con Google OAuth                                      | `feat: google-oauth`     |
| [x]    | Sistema de roles: visitor, author, admin                                          | `feat: roles`            |
| [x]    | Layout del dashboard con navegacion adaptada por rol                              | `feat: dashboard-layout` |
---

## Prioridad 2 — Ideas

| Status | Tarea                                                                 | Git label            |
|--------|-----------------------------------------------------------------------|----------------------|
| [x]    | Modelos y migraciones: Idea, Vote                                     | `feat: ideas-models` |
| [x]    | CRUD de ideas en el dashboard con flujo de aprobacion (solo admin)    | `feat: ideas-crud`   |
| [x]    | Sistema de votacion: votar y desvotar por idea (1 voto por visitante) | `feat: ideas-voting` |
| [x]    | Pagina publica de ideas ordenadas por votos                           | `feat: ideas-public` |
---

## Prioridad 3 — Links

| Status | Tarea                                                | Git label              |
|--------|------------------------------------------------------|------------------------|
| [x]    | Modelos y migraciones: Link, LinkClick               | `feat: links-models`   |
| [x]    | CRUD de links en el dashboard (solo admin)           | `feat: links-crud`     |
| [x]    | Reordenamiento de links (drag & drop u orden manual) | `feat: links-reorder`  |
| [x]    | Conteo y registro de clicks por link en la BD        | `feat: links-tracking` |
| [x]    | Pagina publica de links                              | `feat: links-public`   |
---

## Prioridad 4 — Blog

| Status | Tarea                                                                                        | Git label                           |
|--------|----------------------------------------------------------------------------------------------|-------------------------------------|
| [x]    | Modelos y migraciones: Post, Tag                                                             | `feat: blog-models`                 |
| [x]    | CRUD de posts en el dashboard con permisos por rol                                           | `feat: blog-crud`                   |
| [x]    | Editor Markdown con soporte de bloques de codigo (PHP, HTML, JS)                             | `feat: blog-editor`                 |
| [x]    | Generacion de archivo HTML estatico en storage/posts al publicar                             | `feat: blog-static`                 |
| [x]    | Fix: El contenido markdown desaparece después de salvar / guardar (Re-fix)                   | `fix: blog-markdown-persistence-v2` |
| [x]    | Fix: Error de validación "validation.required" en el editor Markdown (Re-fix v2)             | `fix: blog-editor-validation-final` |
| [x]    | Fix: published_at no se actualiza al publicar un post                                        | `fix: blog-published-at`            |
| [x]    | Pagina publica: post individual servido desde HTML estatico                                  | `feat: blog-public-post`            |
| [x]    | Fix: Error 404 en pagina publica de posts y mejora de verificacion                           | `fix: blog-public-post-404`         |
| [x]    | Mejora: Quitar prefijo "blog/" de la URL pública de posts                                    | `feat: blog-slug-url`               |
| [x]    | Mejora: Resolución de colisiones de slugs y rutas reservadas                                 | `feat: blog-slug-collision`         |
| [x]    | Fix: Sincronización del cursor y error JS en el editor Markdown (Final Fix v5)               | `fix: blog-editor-cursor-sync-v5`   |
| [x]    | Fix: Editor Markdown desaparece por bloqueo de recursos externos (Self-host)                 | `fix: blog-editor-self-host-assets` |
| [x]    | Al generar el HTML del post, usar un layout que contenga el look del home y del sitio        | `feat: blog-integration`            |
| [x]    | Pagina publica: listado del blog con extractos, mas recientes primero. Con el look del sitio | `feat: blog-public-list`            |
| [x]    | Subida y almacenamiento de imagen en la edición del post, de portada en storage/images       | `feat: blog-images`                 |
| [x]    | Scheduler para publicar posts programados automaticamente                                    | `feat: blog-scheduler`              |
---

## Prioridad 5 — Perfil de autor

| Status | Tarea                                                                          | Git label              |
|--------|--------------------------------------------------------------------------------|------------------------|
| [x]    | Perfil del autor en el dashboard: nombre, foto/avatar, bio, cambio de password | `feat: author-profile` |

---

## Fixes

| Status | Tarea                                                                              | Git label       |
|--------|------------------------------------------------------------------------------------|-----------------|
| [x]    | Persiste error en modal de links para guardar en BD URL de links internos, y Autor | `fix: links-DB` |
| [x]    | Select en modales no persiste cuando muestra una opción única que no coincide con la BD (null/visitor): el `<select>` nativo auto-muestra la opción pero no dispara `change`, dejando la propiedad obsoleta. Fix: normalizar owner/post a una opción válida en `openEdit`/`updatedType`. Reproducido y cubierto con test de navegador (Pest v4 + Playwright) | `fix: modal-select-binding` |

----

## Mejoras

| Status | Fecha Agregada | Tarea                                                                                                                                                                                                         | Git label                               |
|--------|----------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|-----------------------------------------|
| [x]    | -              | Redirect post-login: al hacer login desde la pagina de ideas, regresar a ideas en lugar de home                                                                                                               | `fix: ideas-login-redirect`             |
| [x]    | -              | En el archivo HTML de cada post, agregar al final una sección del autor                                                                                                                                       | `feat: blog-show-author`                |
| [x]    | -              | En el modal de links, en dashboard, cuando el link es tipo interno debe mostrar un SELECT o autocompletado para seleccionar uno de los posts                                                                  | `fix: links-intern`                     |
| [x]    | -              | En el modal de links, agregar un SELECT para selecionar el owner del link, que puede ser cualquiera Admin                                                                                                     | `fix: links-owner`                      |
| [x]    | -              | En la página de links donde se muestra un círculo negro el archivo HTML de cada post, mostrar el avatar del owner del primer link                                                                             | `feat: links-show-author`               |
| [x]    | -              | En modal Links tipo interno, el SELECT de posts se despliega bien, se selecciona bien, pero al guardar dice " validation.required"                                                                            | `fix: links-intern`                     |
| [x]    | -              | En modal Links, el SELECT Propietario se despliega bien, se selecciona bien, pero no se guarda en la BD                                                                                                       | `fix: links-owner`                      |
| [x]    | -              | En página de Links, si dan click en algun link, se debe abrir una nueva pestaña para el URL destino                                                                                                           | `feat: links-new-tab`                   |
| [x]    | -              | En modal de Links no se arregló el error "validation.required", ni el owner en BD                                                                                                                             | `fix: links-refix`                      |
| [x]    | -              | En dispositivos como celular, no aparecen en el menu "Links" ni "Ideas"                                                                                                                                       | `fix: menu-links`                       |
| [x]    | -              | En dispositivos como celular, el menu de hambrguesa no funciona                                                                                                                                               | `fix: menu-links`                       |
| [x]    | -              | Si no hay tests para toda la funcionalidad de links, creálos                                                                                                                                                  | `feat: links-test-scripts`              |
| [x]    | -              | CRUD de autores en el dashboard: listar, crear, editar, desactivar usuarios                                                                                                                                   | `feat: users-crud`                      |
| [x]    | -              | Tests para CRUD de autores                                                                                                                                                                                    | `feat: users-tests`                     |
| [x]    | -              | Tags en editor de posts: campo libre con autocompletado y eliminación                                                                                                                                         | `feat: blog-tags`                       |
| [x]    | -              | Mostrar tags en la página pública del blog (listado e individual)                                                                                                                                             | `feat: blog-tags-public`                |
| [x]    | 2026-06-25     | Contacto WhatsApp: Un componente flotante, no invasivo con el ícono de WhatsApp desplegado en todas las páginas. Al hacer clic, redirige a WhatsApp y prellena el mensaje "quiero saber más de [url_origen]". | `feat: whatsapp-floating-button`        |
| [x]    | 2026-06-25     | Cambiar los enlaces que apuntan a la página de Contacto para redirigir directamente a WhatsApp como se indica en el punto anterior (whatsapp-floating-button).                                                | `feat: whatsapp-contact-redirect`       |
| [x]    | 2026-06-25     | Ocultar de la navegación los enlaces "Pricing" e "Ideas", y cambiar "Iniciar sesión" por un texto más discreto.                                                                                               | `feat: navigation-cleanup`              |
| [x]    | 2026-06-25     | Traducir los enlaces de navegación y sus correspondientes rutas/slugs al español.                                                                                                                             | `feat: navigation-spanish-localization` |
| [x]    | 2026-06-25     | Páginas de servicios: modelo Service, migración, seeder, controlador, vista de detalle (/servicios/{slug}) y listado (/servicios). Home actualizado con cards de servicios.                                   | `feat: services-pages`                  |

