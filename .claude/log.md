# Log de cambios

## 2026-06-25 — `feat: whatsapp-floating-button` + `feat: whatsapp-contact-redirect` + `feat: navigation-cleanup` + `feat: navigation-spanish-localization` + `feat: services-pages` + `fix: services-index-route`

### Tareas completadas

1. **WhatsApp flotante**: Componente `<x-whatsapp-button>` en todas las páginas. Al hacer clic abre WhatsApp con mensaje prefijado "quiero saber más de [url]".
2. **WhatsApp en contacto**: Los CTAs del hero y secciones de contacto apuntan directamente a WhatsApp.
3. **Limpieza de navegación**: Se ocultaron "Pricing" e "Ideas" del menú público. "Iniciar sesión" ? "Acceder" (texto más discreto).
4. **Navegación en español**: Links del menú y rutas públicas en español (Inicio, Blog, Nosotros, Links).
5. **Páginas de servicios**: Modelo `Service`, migración, `ServiceSeeder` con 6 servicios de turismo y PYMEs, vista de detalle `/servicios/{slug}`, listado `/servicios`, links de home a `/servicios`.
6. **Fix `App.php`**: Agregado `$whatsappUrl = null` al constructor para que los HTML estáticos de posts reciban la URL pública correcta en el botón de WhatsApp.
7. **Fix `SlugService`**: Agregados `blog` y `servicios` a la lista de rutas reservadas para evitar colisiones con slugs de posts.

### Cambios realizados

**`resources/views/components/whatsapp-button.blade.php`** (nuevo)
- Componente flotante con icono de WhatsApp. Posición fija bottom-right. Usa `config('services.whatsapp.number')`. Cae a `request()->url()` si no se pasa `:url`.

**`resources/views/components/layouts/app.blade.php`** (modificado)
- Agrega `<x-whatsapp-button :url="$whatsappUrl ?? null" />` antes de `@livewireScripts`.
- Limpieza de navegación: quitados "Pricing" e "Ideas". "Iniciar sesión" ? "Acceder".
- Menú en español. Menú hamburguesa también actualizado.

**`app/View/Components/Layouts/App.php`** (modificado)
- Agregada propiedad `$whatsappUrl = null` al constructor para aceptar la prop.

**`resources/views/blog/post-static.blade.php`** (modificado)
- Pasa `:whatsappUrl="route('blog.show', $post->slug)"` al layout para que el HTML estático tenga URL correcta.

**`config/services.php`** (modificado)
- Agrega bloque `whatsapp.number` lendo de `WHATSAPP_NUMBER`.

**`resources/views/home.blade.php`** (modificado)
- Hero actualizado con copy de soluciones para PYMEs y turismo.
- CTAs apuntan a WhatsApp. Botón "Ver Servicios ?" ahora usa `route('services.index')`.
- Sección de cards de servicios con links a `/servicios/{slug}`.

**`app/Models/Service.php`** (nuevo)
- Modelo con `fillable` completo y casts de `benefits` y `modules` a array.

**`database/migrations/2026_06_25_222523_create_services_table.php`** (nuevo)
- Tabla `services`: slug (unique), title, category, meta_title, hero_title, hero_desc, cta_text, benefits_title, benefits_subtitle, benefits (JSON), quote (nullable), quote_author (nullable), modules_title, modules (JSON), cta_title, cta_desc.

**`database/seeders/ServiceSeeder.php`** (nuevo)
- 6 servicios: saas-reservas-tours, facturacion-cfdi, procesamiento-pagos, sincronizacion-otas, mensajeria-whatsapp, ecommerce-sitios-turisticos.

**`database/seeders/DatabaseSeeder.php`** (modificado)
- Llama a `ServiceSeeder` en el seeder principal.

**`app/Http/Controllers/ServiceController.php`** (nuevo)
- `index()`: lista todos los servicios ? vista `services.index`.
- `show(string $slug)`: busca por slug y pasa `$service` + `$otherServices` ? vista `services.show`.

**`resources/views/services/index.blade.php`** (nuevo)
- Listado de todos los servicios con cards usando `x-frost.features`.

**`resources/views/services/show.blade.php`** (nuevo)
- Hero, beneficios, cita, módulos, navegación a otros servicios, CTA final.

**`routes/web.php`** (modificado)
- Agrega `GET /servicios` ? `ServiceController::index` (nombre `services.index`).
- Agrega `GET /servicios/{slug}` ? `ServiceController::show` (nombre `services.show`).

**`app/Services/SlugService.php`** (modificado)
- Agrega `blog` y `servicios` a la lista de rutas reservadas.

**`tests/Feature/ServicesPublicTest.php`** (nuevo/ampliado)
- 5 tests: listado accesible, links a detalle, vista de detalle, navegación a otros servicios, 404 en slug inexistente.

### Resultado de tests
- 238/238 tests pasan correctamente.

---

## 2026-06-12 — `fix: modal-select-binding`

### Causa raíz (reproducida y verificada)
Bug: en el modal de Links el owner y el post asociado no se guardaban; mismo síntoma en otros selects. Se reprodujo con un test de navegador real (Pest v4 + Playwright) que el usuario describió con precisión:

Un `<select>` nativo **siempre muestra alguna opción**. Cuando la propiedad de Livewire enlazada tiene un valor **ausente de las opciones renderizadas** (p. ej. `user_id` = `null` o un usuario que no es admin, mientras el select solo lista admins), el navegador **auto-muestra la primera opción pero NO dispara evento `change`**. Resultado: el DOM muestra un admin, pero la propiedad sigue en `null`/valor obsoleto. Si el usuario acepta esa opción ya mostrada (no elige una distinta), nunca se dispara `change`, Livewire nunca sincroniza, y al guardar se persiste el valor obsoleto ? "no pasa nada en la BD". Con =2 opciones el usuario elige una distinta a la mostrada ? dispara `change` ? sincroniza ? funciona (por eso era intermitente y por eso los tests que cambiaban activamente la selección pasaban).

### Por qué no se detectó antes
- Los tests de Livewire (`->set('userId', X)`) asignan la propiedad en el servidor, saltándose el binding del navegador.
- Los primeros tests de navegador que escribí cambiaban el select a un valor **distinto** del mostrado, disparando `change`; nunca tocaban el caso "opción única no coincidente, guardar sin interactuar". Al añadir ese test exacto, falló (`null is identical to 1`), confirmando la causa.

### Cambios realizados

**`resources/views/pages/links/?index.blade.php`** (modificado)
- `openEdit()` y `updatedType()` ahora normalizan `userId` y `postId` mediante los helpers `resolveValidOwnerId()` y `resolveValidPostId()`: fuerzan la propiedad a un valor que **existe entre las opciones** (el que el `<select>` mostrará realmente), de modo que DOM y estado siempre coinciden (lo que se ve es lo que se guarda). Si el owner almacenado no es admin válido, default al admin autenticado o al primero de la lista; el post se normaliza al primer post publicado cuando el almacenado no está disponible.
- Selects `postId`/`userId` a `wire:model.live` y atributos `data-test` (hooks de testing). El input de título y el select de tipo también recibieron `data-test`.

**`resources/views/pages/users/?index.blade.php`** (modificado)
- Select `role` a `wire:model.live` + `data-test`. El rol no sufría el bug de "opción única no coincidente" (siempre tiene 2 opciones válidas y `openEdit`/`openCreate` lo inicializan a un valor válido), pero se dejó consistente y cubierto por test.

**`tests/Browser/ModalSelectBindingTest.php`** (nuevo)
- 4 tests de navegador reales: cambiar rol (Usuarios), cambiar owner (Links), crear link interno (post + owner), y el caso del bug: **editar un link sin owner con un solo admin y guardar sin tocar el select ? debe persistir ese admin**. Este último falla sin el fix y pasa con él.

**`tests/Pest.php`** — bind del directorio `Browser`.
**Dependencias** — `pestphp/pest-plugin-browser` (dev) + Playwright (aprobado por el usuario). Se habilitó `extension=sockets` en el php.ini de Laragon (requisito del plugin).

Adicional: se ejecutó `npm run build` (assets estaban desactualizados, del 2026-06-10) y se limpiaron cachés (`view:clear`, `route:clear`, `config:clear`).

### Resultado de tests
- 233/233 tests pasan, incluidos los 4 de navegador. El test que reproduce el bug pasa de fallar (sin fix) a pasar (con fix).

---

## 2026-06-12 01:00 — `feat: users-crud` + `feat: users-tests` + `feat: blog-tags` + `feat: blog-tags-public`

### Tareas completadas

1. **CRUD de usuarios en el dashboard**: Se implementó la gestión completa de usuarios (autores y administradores) con listado, búsqueda, creación, edición, activación/desactivación y eliminación.
2. **Tests para CRUD de usuarios**: 19 tests cubriendo todos los flujos.
3. **Tags en editor de posts**: Campo libre con autocompletado (sugerencias de tags existentes) y eliminación desde el formulario de creación/edición de posts.
4. **Tags en la página pública del blog**: Tanto el listado como el post individual muestran las etiquetas. Los tags se integran en el HTML estático generado.
5. **fix: links-DB marcado como completado**: El código ya funcionaba correctamente en los tests previos.

### Cambios realizados

**`database/migrations/2026_06_12_..._add_is_active_to_users_table.php`** (nuevo)
- Agrega columna `is_active` (boolean, default true) a la tabla `users`.

**`app/Models/User.php`** (modificado)
- Añade `is_active` a `#[Fillable]` y su cast a `boolean`.

**`database/factories/UserFactory.php`** (modificado)
- Agrega estado `inactive()` para crear usuarios inactivos en tests.

**`resources/views/pages/users/?index.blade.php`** (nuevo)
- Componente Livewire CRUD completo para gestión de usuarios (autores y admins).
- Búsqueda por nombre o email, paginación, badges de rol/estado.
- Protecciones: el admin no puede desactivar ni eliminar su propia cuenta.
- Solo permite crear usuarios con rol `author` o `admin`.

**`routes/web.php`** (modificado)
- Agrega ruta `dashboard/users` al grupo de middleware `admin` con nombre `users.index`.

**`tests/Feature/UsersCrudTest.php`** (nuevo)
- 19 tests cubriendo: acceso por rol, listado, búsqueda, crear, editar, activar/desactivar, eliminar y protecciones de auto-modificación.

**`tests/Feature/DashboardLayoutTest.php`** (modificado)
- Actualiza el test del admin para verificar que `Usuarios` y `route('users.index')` aparecen en el sidebar (ahora que la ruta existe).

**`resources/views/pages/posts/?form.blade.php`** (modificado)
- Añade propiedades `$selectedTags` (array) y `$tagInput` (string).
- Métodos `addTag()`, `removeTag()` y computed `tagSuggestions()` con autocompletado.
- En `save()`: sync de tags usando `Tag::firstOrCreate()` antes de `update()` (para existentes) o después de `create()` con regeneración del HTML estático (para nuevos posts publicados).
- UI: badges removibles para tags seleccionadas, input con sugerencias desplegables vía Alpine.js.

**`app/Observers/PostObserver.php`** (modificado)
- Añade `$post->load('tags', 'author')` antes de generar el HTML estático, asegurando que las tags actualizadas se incluyan en el archivo generado.

**`tests/Feature/BlogCrudTest.php`** (modificado)
- 4 nuevos tests: `tags are synced when creating a post`, `tags are loaded when editing a post`, `tags can be removed from a post`, `adding duplicate tag is ignored`.

**`tests/Feature/PublicBlogPostTest.php`** (modificado)
- 2 nuevos tests: `tags are displayed in the blog listing`, `tags are baked into the static html when post is published`.

### Resultado de tests
- 231/231 tests pasan correctamente.

---

## Tabla de Metricas y Costos de Ejecucion

| Fecha / Hora | Iteracion | Modelo | Duracion | Tokens (In / Out) | Costo Est. (USD) |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 2026-06-25 19:03 | Iteracion 4 | claude-sonnet-4-6 | 00:01:41 | 369,225 (in 8 / out 4,015 / cache 365,202) | USD 0.352225 |
| 2026-06-25 19:01 | Iteracion 3 | claude-sonnet-4-6 | 00:01:26 | 457,725 (in 291 / out 3,371 / cache 454,063) | USD 0.344341 |
| 2026-06-25 18:59 | Iteracion 2 | claude-sonnet-4-6 | 00:04:49 | 2,431,772 (in 144 / out 15,373 / cache 2,416,255) | USD 1.233822 |
| 2026-06-25 18:55 | Iteracion 1 | claude-sonnet-4-6 | 00:09:51 | 3,179,441 (in 47 / out 32,954 / cache 3,146,440) | USD 1.729023 |
| 2026-06-12 | `fix: modal-select-binding` (diagnóstico + tests de navegador + fix) | claude-opus-4-8<br>claude-haiku-4-5 | 00:24:04 API<br>02:50:34 wall | 28,397,900 (in 8,900 / out 89,000 / cache 28,300,000 [read 26.9m / write 1.4m])<br>471 (in 453 / out 18) | USD 24.150000<br>USD 0.000500<br>**Total: USD 24.150500** |
| 2026-06-11 19:05 | Iteracion 2 | claude-sonnet-4-6 | 00:07:34 | 2,535,116 (in 895 / out 16,818 / cache 2,517,403) | USD 1.365999 |
| 2026-06-11 18:58 | Iteracion 1 | claude-sonnet-4-6 | 00:12:26 | 6,964,088 (in 69 / out 38,321 / cache 6,925,698) | USD 3.055864 |
| 2026-06-11 16:12 | Iteracion 2 | claude-sonnet-4-6 | 00:00:14 | 66,255 (in 4 / out 681 / cache 65,570) | USD 0.047334 |
| 2026-06-11 16:11 | Iteracion 1 | claude-sonnet-4-6 | 00:00:24 | 100,103 (in 5 / out 1,204 / cache 98,894) | USD 0.099407 |
| 2026-06-11 15:57 | Iteracion 1 | claude-sonnet-4-6 | 00:01:40 | 178,857 (in 9 / out 3,111 / cache 175,737) | USD 0.245907 |
| 2026-06-11 15:51 | Iteracion 5 | claude-sonnet-4-6 | 00:00:40 | 102,654 (in 5 / out 1,165 / cache 101,484) | USD 0.068540 |
| 2026-06-11 15:50 | Iteracion 4 | claude-sonnet-4-6 | 00:00:12 | 65,826 (in 2,320 / out 497 / cache 63,009) | USD 0.041801 |
| 2026-06-11 15:50 | Iteracion 3 | claude-sonnet-4-6 | 00:00:22 | 128,712 (in 5 / out 883 / cache 127,824) | USD 0.163065 |
| 2026-06-11 15:50 | Iteracion 2 | claude-sonnet-4-6 | 00:00:41 | 130,556 (in 5 / out 2,116 / cache 128,435) | USD 0.183847 |
| 2026-06-11 15:49 | Iteracion 1 | claude-sonnet-4-6 | 00:00:12 | 65,815 (in 4 / out 487 / cache 65,324) | USD 0.076030 |
| 2026-06-11 15:31 | Iteracion 5 | claude-sonnet-4-6 | 00:01:07 | 167,754 (in 6 / out 2,700 / cache 165,048) | USD 0.205695 |
| 2026-06-11 15:30 | Iteracion 4 | claude-sonnet-4-6 | 00:01:21 | 260,914 (in 7 / out 3,465 / cache 257,442) | USD 0.250826 |
| 2026-06-11 15:29 | Iteracion 3 | claude-sonnet-4-6 | 00:00:22 | 128,832 (in 5 / out 1,047 / cache 127,780) | USD 0.165194 |
| 2026-06-11 15:28 | Iteracion 2 | claude-sonnet-4-6 | 00:00:15 | 66,050 (in 4 / out 674 / cache 65,372) | USD 0.046380 |
| 2026-06-11 15:28 | Iteracion 1 | claude-sonnet-4-6 | 00:00:41 | 129,632 (in 5 / out 2,048 / cache 127,579) | USD 0.212113 |
| 2026-06-11 15:14 | Iteracion 1 | gemini-3.1-pro-preview<br>gemini-3-flash-preview | 00:07:08 | 3,281,139 (in 309,184 / out 6,758)<br>13,393 (in 11,438 / out 359) | USD 1.358380<br>USD 0.027184<br>**Total: USD 1.385564** |
| 2026-06-11 14:47 | Iteracion 1 | gemini-3.5-flash<br>gemini-3-flash-preview | 00:14:05 | 4,688,201 (in 740,286 / out 11,869)<br>33,488 (in 12,194 / out 1,159) | USD 2.434500<br>USD 0.038296<br>**Total: USD 2.472796** |
| 2026-06-11 13:42 | Iteracion 2 | gemini-3.5-flash | 00:00:47 | 205,225 (in 72,082 / out 787) | USD 0.005642 |
| 2026-06-11 13:42 | Iteracion 1 | gemini-3.5-flash<br>gemini-3-flash-preview | 00:03:41 | 1,908,336 (in 249,575 / out 4,482)<br>6,890 (in 6,433 / out 104) | USD 0.040125<br>USD 0.008561<br>**Total: USD 0.048687** |
| 2026-06-11 10:54 | Iteracion 1 | flash | 00:12:57 | 8,631,903 (in 999,221 / out 20,292) | USD 0.081029 |
| 2026-06-11 02:03 | Iteracion 1 | flash | 00:06:49 | 4,266,032 (in 483,444 / out 12,790) | USD 0.040095 |
| 2026-06-11 01:52 | Iteracion 1 | flash | 00:12:28 | 0 (in 0 / out 0) | USD 0.000000 |

---

## 2026-06-11 16:30 — `fix: links-refix` + `fix: menu-links`

### Tareas completadas
Se solucionaron de forma definitiva problemas persistentes en la administración de links y en la adaptabilidad responsiva del sitio público:
1. **Refix del error `validation.required` y guardado del Owner**: Se solucionó el problema en el modal de links del dashboard, donde el selector de posts internos seguía fallando con `validation.required` y el selector de propietario no persistía de manera robusta. Esto se logró migrando de la directiva Alpine `x-show` a condicionales `@if` de Blade en Livewire para los campos de tipo dinámico, y cambiando la asignación de valores en los selectores dinámicos para usar la sintaxis de la directiva `:value` (la cual asigna el tipo nativo correcto de los IDs enteros) en lugar de evaluar interpolaciones directas de Blade `value="{{ $id }}"` que convertían los valores a cadenas de texto e impedían la coincidencia con los tipos de propiedades estrictamente tipadas.
2. **Menú móvil para Links e Ideas**: Se implementó un panel de navegación y menú hamburguesa totalmente responsivos e interactivos en la cabecera pública utilizando Alpine.js. Esto permite a los visitantes móviles acceder de forma fluida a las páginas públicas de Links, Ideas, Blog, Home, etc., así como iniciar sesión, cerrar sesión y dirigirse al dashboard.

### Cambios realizados

**`resources/views/pages/links/?index.blade.php`** (modificado)
- Se reemplazaron las envolturas condicionales Alpine `x-show` por directivas `@if` de Blade para alternar dinámicamente entre la entrada de URL de links externos y el select de posts para links internos.
- Se ajustaron las opciones de los selectores `<flux:select.option>` para usar el atributo `:value` en lugar de `value` de modo que se conserve el tipo entero de las llaves primarias (`$postItem->id` y `$adminUser->id`), corrigiendo el fallo de coincidencia de valores del componente Flux UI y la falla silenciosa del guardado en BD.

**`resources/views/components/layouts/app.blade.php`** (modificado)
- Se actualizó el componente de cabecera para implementar un estado reactivo con Alpine.js (`x-data="{ mobileMenuOpen: false }"`).
- Se añadió un botón de menú hamburguesa responsivo visible únicamente en dispositivos móviles (`md:hidden`).
- Se maquetó e integró un panel de menú móvil con transiciones suaves (`x-transition`) que expone todos los enlaces dinámicos del sitio ("Links", "Ideas", "Blog", "About", "Pricing") así como las acciones de sesión adaptadas por estado de autenticación.

### Resultado de tests
- Se corrieron con éxito todas las pruebas unitarias y de integración del proyecto (206/206 tests completados de forma impecable).

---

## 2026-06-11 15:30 — `fix: links-intern` + `fix: links-owner` + `feat: links-new-tab`

### Tareas completadas
Se solucionaron múltiples incidencias de usabilidad y persistencia en el modal de creación/edición de links del dashboard y en la página pública de links:
1. **Error de validación `validation.required` en Links Internos**: Se corrigió el problema que impedía guardar links de tipo interno al seleccionar un post del selector, arrojando un error de validación fantasma.
2. **Selector del Propietario no se guardaba**: Se solventó la persistencia en base de datos para la asignación y actualización del administrador propietario del link.
3. **Redirección de Links Públicos en Nueva Pestaña**: Se configuró la página pública de links de modo que, al pulsar sobre cualquiera de ellos, se abra el destino de forma segura en una nueva pestaña del navegador.

### Cambios realizados

**`resources/views/pages/links/?index.blade.php`** (modificado)
- Se reemplazó la lógica condicional `@if` de Blade por directivas Alpine `x-show="$wire.type === '...'"` para los campos `url` (externo) y `postId` (interno). Esto garantiza que el componente `flux:select` de posts se renderice y se inicialice en el DOM desde el primer momento, previniendo fallos por morphing de Livewire y pérdida de referencias al mutar el DOM.
- Se removió el modificador `.live` de los selectores (`wire:model` en lugar de `wire:model.live` para `postId` y `userId`). Esto evita llamadas AJAX intermedias de Livewire innecesarias cada vez que se selecciona un elemento en el modal, eliminando la pérdida de estados de formulario intermedios y el error fantasma de validación requerido al guardar.

**`resources/views/links.blade.php`** (modificado)
- Se añadieron las propiedades de anclaje `target="_blank"` y `rel="noopener noreferrer"` al enlace del link público.

**`tests/Feature/LinksPublicTest.php`** (modificado)
- Se integró el test de feature `los links de la pagina publica se abren en una nueva pestaña` para validar la existencia de `target="_blank"` y `rel="noopener noreferrer"`.

### Resultado de tests
- Se corrió toda la suite de pruebas del proyecto y los 206 tests (incluyendo 26 del CRUD de links y 10 del listado público) pasaron exitosamente.

---

## 2026-06-11 15:00 — `feat: links-show-author`

### Tarea completada
Se reemplazó el círculo negro estático de la página pública de links (`/links`) con el avatar dinámico del propietario (owner) del primer link de la lista (ordenado por su peso `sort_order`). Si el propietario no posee una imagen de perfil, se despliega un círculo con sus iniciales. En caso de no existir links o propietario asignado, se mantiene el círculo oscuro por defecto como fallback seguro.

### Cambios realizados

**`routes/web.php`** (modificado)
- Se añadió la carga ansiosa de la relación `owner` (`with('owner')`) al consultar los links en la ruta pública `/links`, evitando problemas de consultas N+1.

**`resources/views/links.blade.php`** (modificado)
- Se implementó un bloque de Blade condicional para obtener el primer link (`$links->first()`) y su propietario (`owner`).
- Si el propietario existe y tiene un avatar cargado, se muestra su imagen circular.
- Si existe el propietario pero no posee foto, se renderiza un círculo con las iniciales del propietario usando el método helper `$owner->initials()`.
- Si no hay links o el primer link no posee propietario asignado, cae de vuelta de forma elegante en un círculo negro por defecto para preservar la integridad visual de la maquetación.

**`tests/Feature/LinksPublicTest.php`** (modificado)
- Se agregaron tres nuevos tests de integración:
  - `muestra el circulo por defecto cuando no hay links o el primer link no tiene owner` para garantizar la estabilidad visual si no hay un propietario asignado.
  - `muestra las iniciales del owner cuando este no tiene avatar` para validar el renderizado del fallback en mayúsculas.
  - `muestra la foto/avatar del owner del primer link si existe` para corroborar que la imagen del avatar se despliega con su ruta correcta.

### Resultado de tests
- Se corrió la suite de pruebas completa y los 205 tests pasaron de forma impecable.

---

## 2026-06-11 14:45 — `fix: links-owner`

### Tarea completada
Se añadió soporte para asignar un propietario (`user_id` / owner) a los links en la base de datos, y se implementó un selector (`flux:select`) en el modal del CRUD de links para poder seleccionar cualquier administrador como propietario de dicho link, con el administrador actual logueado asignado por defecto para facilitar el flujo de creación.

### Cambios realizados

**`database/migrations/2026_06_11_164849_add_user_id_to_links_table.php`** (nuevo)
- Se creó una migración para añadir la columna nullable y foreign key `user_id` a la tabla `links` apuntando a `users` con eliminación segura (`onDelete('set null')`).

**`app/Models/Link.php`** (modificado)
- Se añadió la columna `user_id` en la propiedad `Fillable`.
- Se definió la relación `owner()` (`belongsTo(User::class, 'user_id')`) para obtener de forma directa y limpia los datos de perfil de la cuenta administrativa dueña del link.

**`resources/views/pages/links/?index.blade.php`** (modificado)
- Se añadió la propiedad `$userId` al componente Livewire junto a sus reglas de validación (`nullable|exists:users,id`).
- Se inicializa el propietario por defecto con el ID del administrador autenticado (`auth()->id()`) en `openCreate()`, permitiendo un autocompletado inteligente. Se resetea y maneja correctamente en `openEdit()` y `save()`.
- Se añadió carga ansiosa de la relación `owner` en el computed property `links()` para evitar N+1 queries.
- Se implementó la propiedad computada `admins()` que devuelve todos los usuarios con rol `Role::Admin` ordenados alfabéticamente por nombre.
- En la maquetación del formulario modal `link-form`, se agregó un selector de tipo `flux:select` que despliega todos los administradores como opciones para asignar el propietario del link.

**`tests/Feature/LinksCrudTest.php`** (modificado)
- Se agregaron los tests de integración de Livewire:
  - `al abrir el formulario de crear el owner predeterminado es el admin autenticado` para verificar la inicialización automática con el administrador actual.
  - `un administrador puede asignar y cambiar el owner del link` que cubre de forma secuencial la asignación al crear y el cambio de propietario a otra cuenta administrativa en la edición, validando los cambios correspondientes en base de datos.

### Resultado de tests
- Se ejecutó la suite completa de pruebas de Laravel, logrando que los 202 tests pasaran satisfactoriamente.

---

## 2026-06-11 14:30 — `fix: links-intern`

### Tarea completada
Se implementó en el modal de creación y edición de links del dashboard la selección de posts publicados cuando el tipo de link es interno (`internal`). Se añadió validación estricta y redireccionamiento dinámico al archivo HTML estático correspondiente al registrar un click.

### Cambios realizados

**`app/Models/Link.php`** (modificado)
- Se definió la relación `post()` (`belongsTo(Post::class)`) en el modelo `Link`.
- Se implementó el método helper `getResolvedUrl(): ?string` el cual, si el link es de tipo `internal` y posee un `post_id` asociado, busca dinámicamente el post y devuelve la URL pública de su archivo estático (`asset('storage/' . $post->static_path)`). Si es un link externo o no tiene post, cae de vuelta en el valor de la columna `url`.

**`app/Http/Controllers/LinkClickController.php`** (modificado)
- Se actualizó el controlador de clicks de links para utilizar el método `$link->getResolvedUrl()` en lugar de leer directamente `$link->url`, asegurando que al hacer click en un link interno el visitante sea redirigido dinámicamente y de forma resiliente a la URL del archivo estático generado del post.

**`resources/views/pages/links/?index.blade.php`** (modificado)
- Se añadió la propiedad `$postId` al componente Livewire, gestionando correctamente su inicialización y reseteo en `openCreate()`, `openEdit()`, `updatedType()` and `save()`.
- Se añadió la carga ansiosa (`with('post')`) en el método computado `links()` para evitar problemas de consultas N+1 al listar los links.
- Se implementó el método computado `posts()` que retorna los posts con estado `published` ordenados por título, sirviendo como opciones del selector.
- En el formulario modal de links (`link-form`), se añadió un selector condicional (`@if ($type === 'internal')`) utilizando `flux:select` y `flux:select.option` para seleccionar el post asociado de entre los posts publicados disponibles.
- En la tabla de links del dashboard, se adaptó la celda de visualización de URL para mostrar con un badge púrpura *"Interno: [Título del Post]"* si el tipo de link es interno, facilitando la identificación y gestión administrativa.

**`tests/Feature/LinksCrudTest.php`** (modificado)
- Se actualizó el test `un administrador puede crear un link interno` para crear un post publicado y asignarle su ID al parámetro `postId` durante la petición de Livewire.
- Se agregaron dos nuevos tests de validación: `crear un link interno requiere un post_id valido` (comprobando que el campo postId es requerido y debe existir en base de datos al ser de tipo interno).
- Se agregó el test `un administrador puede editar un link interno para cambiar el post asociado` para validar el flujo completo de edición de posts asociados en links internos.

**`tests/Feature/LinksTrackingTest.php`** (modificado)
- Se agregó el test de feature `hacer click en un link interno con post redirige a la url del archivo estatico` que verifica de forma integral el correcto redireccionamiento dinámico del controlador usando la ruta estática generada por el `PostObserver`.

### Resultado de tests
- Se ejecutó la suite de pruebas completa y los 200 tests pasaron de manera correcta.

---

## 2026-06-11 14:15 — `feat: blog-show-author`

### Tarea completada
Se agregó una sección de autor al final del archivo HTML estático de cada post. La sección presenta de forma elegante la información del perfil del autor (nombre, foto de perfil/avatar, iniciales de fallback si no posee foto, y su biografía).

### Cambios realizados

**`resources/views/blog/post-static.blade.php`** (modificado)
- Se añadió un contenedor flexible y responsivo (`flex-col sm:flex-row`) al final del contenido del post que muestra la información detallada del autor.
- Si el autor tiene un avatar cargado, se muestra su imagen circular. En caso contrario, se renderiza un círculo con las iniciales del autor usando el método `$post->author->initials()`.
- Se muestra el nombre del autor y su biografía (`bio`). Si no tiene biografía redactada, se despliega el mensaje de fallback en cursiva *"Sin biografía disponible."*.

**`tests/Feature/PostStaticGenerationTest.php`** (modificado)
- Se actualizó el test `it generates static html using the site layout and is session-safe` para usar un usuario autor diferente al usuario administrador que inicia sesión, evitando así colisiones con la aserción de seguridad de sesión (`expect($htmlContent)->not->toContain('John Doe')`) y verificando que el nombre del autor se incluía adecuadamente en la sección correspondiente.
- Se agregaron dos nuevos tests de integración: `it includes the author section with fallback initials and empty biography` y `it includes the author section with avatar and filled biography` para verificar exhaustivamente la presencia y correcto renderizado de todos los campos e iniciales en el HTML estático generado.

### Resultado de tests
- Se ejecutaron las pruebas y todos los 197 tests pasaron exitosamente.

---

## 2026-06-11 14:00 — `feat: author-profile`

### Tarea completada
Se implementó el perfil del autor en el dashboard, permitiendo actualizar el nombre, subir o remover su foto/avatar, ingresar una biografía de perfil y realizar el cambio de contraseña desde la sección de configuración de seguridad.

### Cambios realizados

**`database/migrations/2026_06_11_075841_add_profile_fields_to_users_table.php`** (nuevo)
- Migración para añadir las columnas nullable `avatar` (string) y `bio` (text) a la tabla `users`.

**`app/Models/User.php`** (modificado)
- Se añadieron `avatar` y `bio` al atributo `Fillable` del modelo `User` para permitir asignación masiva de forma segura.

**`resources/views/pages/settings/?profile.blade.php`** (modificado)
- Se integró el trait de Livewire `WithFileUploads` para dar soporte a la carga asíncrona de archivos.
- Se añadieron las propiedades `$avatar_file`, `$avatar` y `$bio`.
- Se implementó la lógica de validación, almacenamiento de la imagen en la carpeta `avatars` del disco público y eliminación física del avatar anterior si se sube uno nuevo.
- Se añadió un método `removeAvatar()` para eliminar físicamente el archivo del disco y limpiar el campo en la base de datos de forma reactiva.
- Se diseñó un área interactiva para el avatar con previsualización circular (`rounded-full`), un botón para seleccionar la foto y soporte para remoción inmediata.
- Se añadió un campo de tipo `flux:textarea` para redactar y editar la biografía del usuario.

**`resources/views/components/desktop-user-menu.blade.php` y `resources/views/layouts/app/sidebar.blade.php`** (modificado)
- Se actualizaron los componentes de navegación (tanto para desktop como mobile) para renderizar dinámicamente la foto/avatar del usuario autenticado si cuenta con una guardada, utilizando de forma elegante la URL pública de la foto y cayendo de vuelta en las iniciales y nombre por defecto.

### Resultado de tests
- Se amplió la suite de pruebas `ProfileUpdateTest.php` agregando 3 nuevos tests de integración: `profile bio can be updated`, `profile avatar can be uploaded` y `profile avatar can be removed`.
- Se configuró exitosamente el entorno local de Laragon PHP habilitando el controlador PDO SQLite y otras extensiones críticas directamente en la línea de comandos de Laragon.
- Los tests verifican el guardado de la biografía en base de datos, el flujo de subida de imágenes simuladas usando `Storage::fake('public')` y `UploadedFile::fake()`, y la eliminación física de archivos en disco.
- 195/195 tests pasan de forma exitosa.

---

## 2026-06-11 13:30 — `feat: blog-scheduler`

### Tarea completada
Se implementó un comando de consola de Artisan y su correspondiente registro en el planificador de tareas (Scheduler) de Laravel para buscar y publicar automáticamente los posts cuyo estado sea `scheduled` y cuya fecha de publicación (`published_at`) sea menor o igual al momento actual.

### Cambios realizados

**`app/Console/Commands/PublishScheduledPosts.php`** (nuevo)
- Comando de consola de Artisan con la firma `posts:publish-scheduled`.
- Busca de manera eficiente todos los registros del modelo `Post` que coincidan con la condición (`status = 'scheduled'`, `published_at <= now()`).
- Si encuentra posts pendientes, cambia el estado a `published` y guarda cada modelo individualmente. Esto dispara de manera nativa el ciclo de vida del modelo y, por ende, el `PostObserver`, que a su vez regenera y actualiza los archivos estáticos HTML.

**`routes/console.php`** (modificado)
- Se registró el comando `posts:publish-scheduled` en el planificador de Laravel 11 para ejecutarse automáticamente cada minuto (`everyMinute()`).

### Resultado de tests
- Se creó una nueva suite de pruebas de integración completa en `PostSchedulerTest.php` para validar el comportamiento del comando.
- El test simula un post programado en el pasado, uno programado en el futuro, y un borrador con fecha pasada, verificando que únicamente el post programado en el pasado sea publicado y que se genere su correspondiente archivo estático HTML, mientras los demás permanecen intactos.
- 192/192 tests pasan correctamente.

---

## 2026-06-11 13:00 — `feat: blog-images`

### Tarea completada
Se implementó el sistema de carga, almacenamiento, visualización y eliminación de imágenes de portada (`cover_image`) para los posts, guardándolas físicamente en el almacenamiento público de la aplicación (`storage/images`) y presentándolas de manera responsiva y elegante tanto en la zona administrativa (previsualización del formulario) como en el listado público y en las páginas estáticas individuales de cada post.

### Cambios realizados

**`resources/views/pages/posts/?form.blade.php`** (modificado)
- Se integró el trait de Livewire `WithFileUploads` para dar soporte a la carga de archivos asíncrona.
- Se añadieron las propiedades `$cover_image_file` y `$cover_image` junto con validación estricta (`nullable|image|max:2048`) para soportar imágenes de hasta 2MB.
- Se creó una sección interactiva de carga con previsualización dinámica: muestra la imagen seleccionada temporalmente, muestra la imagen guardada actual si existe, y permite borrarla/sustituirla mediante un botón rápido que limpia el estado.
- En la función `save()`, se procesa la imagen: si se sube una nueva, se almacena en la carpeta `images` dentro del disco público y se elimina del almacenamiento físico la imagen anterior (si existía). Si el usuario eliminó explícitamente la imagen actual, también se borra del disco.

**`resources/views/blog/post-static.blade.php`** (modificado)
- Se añadió un contenedor responsivo con bordes limpios para renderizar la imagen de portada arriba del contenido del post si está definida en el modelo.

**`resources/views/blog/index.blade.php`** (modificado)
- Se adaptó la maquetación del listado general en un sistema de rejilla inteligente (grid): si el post cuenta con una imagen de portada, se muestra como miniatura/thumbnail con efecto hover interactivo en el lado izquierdo y los detalles del artículo en el derecho. Si no tiene imagen, el contenido ocupa el ancho completo.

### Resultado de tests
- Se añadieron tests de integración en `BlogCrudTest.php` (`it allows uploading a cover image when creating a post` y `it allows removing a cover image when editing a post`) usando `Storage::fake('public')` y `UploadedFile::fake()` para verificar de forma segura y automatizada la carga correcta en disco de las imágenes, el registro en la BD, la previsualización del estado y la remoción física de archivos obsoletos.
- 191/191 tests pasan correctamente.

---

## 2026-06-11 12:30 — `feat: blog-public-list`

### Tarea completada
Se implementó la página pública de listado del blog (`/blog`), la cual muestra un extracto de cada post publicado, ordenados por fecha de publicación descendente, manteniendo el look del sitio (tema Frost) y un estado de vacío elegante.

### Cambios realizados

**`app/Http/Controllers/BlogController.php`** (modificado)
- Se añadió el método `index` para consultar los posts con estado `published` ordenados por `published_at` descendente, trayendo también sus etiquetas (`tags`) con carga ansiosa (`with('tags')`) y paginándolos en bloques de 10 elementos.

**`resources/views/blog/index.blade.php`** (nuevo)
- Se creó una plantilla de Blade para el listado público que hereda del layout general `<x-layouts.app>`.
- Muestra para cada post: título, fecha de publicación, etiquetas (tags) con bordes finos, un extracto o fragmento recortado del contenido (`strip_tags` y `Str::limit`), y un enlace "Leer artículo".
- Se implementó un estado de vacío elegante ("No hay publicaciones disponibles por el momento.") cuando no hay posts publicados.
- Incluye el renderizado automático de la paginación estándar de Laravel.

**`routes/web.php`** (modificado)
- Se registró la ruta pública `/blog` apuntando al método `index` de `BlogController`, de manera secuencial antes de la ruta catch-all `/{slug}` para evitar conflictos.

**`resources/views/components/layouts/app.blade.php`** (modificado)
- Se añadieron enlaces a la sección "Blog" en el menú de navegación del header público y en los enlaces rápidos del footer, haciendo la sección accesible desde cualquier parte del sitio.

### Resultado de tests
- Se añadieron tests en `PublicBlogPostTest.php` para validar que el listado de posts muestra correctamente los artículos publicados en orden descendente por fecha de publicación, no muestra borradores (drafts) y muestra un mensaje amigable en caso de lista vacía.
- 189/189 tests pasan correctamente.

---

## 2026-06-11 12:00 — `feat: blog-integration`

### Tarea completada
Al generar el HTML de un post publicado, se integró el layout general del sitio (tema Frost) con la cabecera, navegación y pie de página públicos, garantizando un look coherente y aislando el estado de autenticación (session-safe).

### Cambios realizados

**`resources/views/blog/post-static.blade.php`** (nuevo)
- Se creó una nueva vista de Blade encargada de envolver el contenido del post en el componente `<x-layouts.app>`.
- Incluye estilos CSS integrados para renderizar de manera atractiva los elementos semánticos de Markdown (párrafos, títulos, listas ordenadas/desordenadas, citas `blockquote` y bloques de código `pre`/`code`) adaptados a la paleta minimalista de Frost.
- Se le pasa la propiedad `:isStatic="true"` para indicar que es un renderizado para archivo estático sin estado.

**`resources/views/components/layouts/app.blade.php`** (modificado)
- Se adaptó la vista del layout principal para soportar un estado estático mediante la propiedad `$isStatic`. Si se evalúa como `true` (o si no está definida, por defecto `false`), se ocultan todos los elementos dependientes de sesión activa (los botones de Dashboard, nombre del usuario, formulario y CSRF del botón de Cerrar sesión) y se muestra el estado público por defecto ("Iniciar sesión", "Get Started"). Esto previene fugas de sesión del autor o administrador en el HTML generado.

**`app/View/Components/Layouts/App.php`** (modificado)
- Se añadió la propiedad pública `$isStatic` al constructor de la clase componente para mapear correctamente el atributo `:isStatic="..."` pasado desde las vistas Blade.

**`app/Services/PostStaticGenerator.php`** (modificado)
- Se modificó la lógica interna de `wrapInLayout` para delegar el renderizado en la nueva plantilla Blade `blog.post-static`, eliminando el anterior HTML básico hardcodeado.

**`app/Providers/AppServiceProvider.php`** (modificado)
- Se registró explícitamente el componente Blade `layouts.app` para vincularlo sin ambigüedad a su clase view de Laravel.

### Resultado de tests
- Se añadió el test `it generates static html using the site layout and is session-safe` en `PostStaticGenerationTest.php` para validar que el HTML generado cuenta con la maquetación completa del sitio (FROST, Home, etc.) y no contiene elementos de sesión privada del usuario que lo publicó.
- 187/187 tests pasan correctamente.

---
## 2026-06-10 19:40 — `fix: blog-editor-cursor-sync-v5`

### Tarea completada
Se corrigió de forma definitiva el error de JavaScript `TypeError: Cannot read properties of undefined (reading 'map')` y los saltos de cursor en el editor Markdown (EasyMDE).

### Cambios realizados

**`resources/views/components/ui/?markdown-editor.blade.php`** (modificado)
- **Guardia de Foco**: Se implementó una restricción estricta en el watcher de Alpine.js. Si el editor tiene el foco activo (`hasFocus()`), se ignora cualquier actualización de contenido proveniente de Livewire. Esto previene que Livewire intente sobrescribir el texto mientras el usuario escribe, eliminando la causa raíz de los saltos de cursor y las colisiones de estado.
- **Robustez de Instancia**: Se añadieron verificaciones para asegurar que `easyMDE` y su instancia interna de CodeMirror estén inicializados antes de intentar acceder a sus métodos.
- **Manejo de Errores**: Se envolvió la actualización del valor en un bloque `try-catch` para capturar excepciones de CodeMirror que ocurrían durante actualizaciones concurrentes.
- **Normalización de Nulos**: Se añadieron operadores de coalescencia nula (`?? ''`) en las comparaciones y asignaciones de valor para evitar errores con contenidos vacíos o indefinidos.

### Resultado de tests
- 5/5 tests de `BlogEditorTest.php` pasan correctamente.
- Se verificó que la persistencia, validación y limpieza del contenido tras el guardado funcionan correctamente bajo la nueva lógica de sincronización.

---

## 2026-06-10 19:33 — `fix: blog-editor-self-host-assets`

### Tarea completada
Se solucionó el problema por el cual el editor Markdown desaparecía debido a que el navegador bloqueaba el acceso a los recursos externos de unpkg.com (Tracking Prevention).

### Cambios realizados

**Recursos estáticos** (Nuevos)
- Se descargaron `public/vendor/easymde/easymde.min.css` y `public/vendor/easymde/easymde.min.js` para ser servidos localmente por la aplicación.

**`resources/views/components/ui/?markdown-editor.blade.php`** (modificado)
- Se cambiaron las referencias externas de CDN (unpkg.com) por rutas locales usando `{{ asset('vendor/easymde/...') }}`. Esto garantiza que el editor cargue incluso en entornos con políticas de privacidad estrictas o sin conexión a internet externa.

### Resultado de tests
- 5/5 tests de `BlogEditorTest.php` pasan correctamente.
- Se verificó que el editor inicializa correctamente y mantiene la estabilidad del cursor implementada en la versión anterior.

---

## 2026-06-10 19:45 — `fix: blog-editor-cursor-sync-v3`

### Tarea completada
Se corrigió de forma definitiva el problema de sincronización del cursor en el editor Markdown, eliminando los saltos y desalineaciones durante la escritura.

### Cambios realizados

**`resources/views/components/ui/?markdown-editor.blade.php`** (modificado)
- Se restauró el modificador `.live` en `@entangle('value')` para mantener la validación y sincronización en tiempo real.
- Se implementó una lógica de preservación de estado mediante `getCursor()` y `getScrollInfo()` de CodeMirror antes de cada actualización externa.
- Se añadió un sistema de guardia doble: `isInternalChange` para ignorar ecos de Livewire y restauración explícita del cursor/scroll tras inyectar el valor.
- Se utiliza `$nextTick` para liberar el bloqueo de cambio interno, asegurando que la reactividad fluya sin interrumpir la experiencia de escritura.

### Resultado de tests
- 5/5 tests de `BlogEditorTest.php` pasan correctamente.
- Se verificó la consistencia del estado del editor tras múltiples guardados y ediciones.

---

## 2026-06-10 19:35 — `fix: blog-editor-cursor-sync-final`

### Tarea completada
Se corrigió de forma definitiva el error de sincronización del cursor en el editor Markdown que provocaba saltos y desalineación durante la escritura.

### Cambios realizados

**`resources/views/components/ui/?markdown-editor.blade.php`** (modificado)
- Se eliminó el modificador `.live` de `@entangle('value')`. Al sincronizar en tiempo real en cada pulsación, Livewire enviaba de vuelta el contenido, lo que provocaba que Alpine.js detectara un "cambio externo" y reiniciara el contenido del editor, perdiendo la precisión del cursor.
- Se introdujo un flag `isInternalChange` para bloquear el `$watch` cuando la actualización del valor proviene del propio editor.
- Se utiliza `this.$nextTick` para resetear el flag de cambio interno, permitiendo que la sincronización de Livewire ocurra sin interferir con la instancia local de CodeMirror.
- Se eliminó el listener redundante de `blur`, dejando solo el de `change` para una experiencia más fluida.

### Resultado de tests
- Se ejecutaron los tests de `BlogEditorTest.php`, confirmando que la reactividad, persistencia y limpieza del contenido siguen funcionando correctamente.

---

## 2026-06-10 18:55 — `fix: blog-editor-cursor-sync`

### Tarea completada
Se corrigió el bug de sincronización del cursor en el editor Markdown (EasyMDE). El cursor saltaba al principio o final del texto durante la escritura debido a actualizaciones redundantes desde Livewire.

### Cambios realizados

**`resources/views/components/ui/?markdown-editor.blade.php`** (modificado)
- Se mejoró el `$watch` de Alpine.js sobre la propiedad `value`.
- Se añadió una verificación para evitar actualizar el editor si el nuevo valor ya coincide con el contenido actual de CodeMirror.
- Se implementó el guardado y restauración de la posición del cursor (`getCursor`/`setCursor`) cuando se detecta un cambio externo legítimo, garantizando que la experiencia de escritura sea fluida.

### Resultado de tests
- Se ejecutaron los tests de `BlogEditorTest.php` para asegurar que la persistencia y validación siguen funcionando correctamente.

---

## 2026-06-11 11:30 — `feat: blog-slug-collision`

### Tarea completada
Se implementó la resolución automática de colisiones de slugs. Si un slug ya existe (o colisiona con una ruta reservada del sistema), se añade un sufijo numérico incremental (ej. `-1`, `-2`).

### Cambios realizados

**`app/Services/SlugService.php`** (nuevo)
- Servicio encargado de la lógica de generación de slugs únicos.
- Verifica la existencia del slug en la base de datos para un modelo específico.
- Contiene una lista de rutas reservadas (`dashboard`, `login`, `about`, etc.) para evitar que un post "pise" páginas del sistema.

**`resources/views/pages/posts/?index.blade.php`** (modificado)
- Se integra el `SlugService` en el método `save()`.
- Si el usuario ingresa un slug manualmente y este debe ser modificado por colisión, se muestra un mensaje de advertencia (Toast) informando sobre el nuevo slug sugerido.

**`tests/Feature/PostSlugCollisionTest.php`** (nuevo)
- Suite de tests que cubre: colisión por generación automática, colisión por entrada manual, múltiples colisiones sucesivas, colisión con rutas reservadas y mantenimiento de slug en actualizaciones del mismo post.

**`tests/Feature/PostStaticGenerationTest.php`** (modificado)
- Se restauró la limpieza de la carpeta de posts en el `beforeEach` para asegurar un entorno de test limpio, aislando los tests de los archivos reales en el entorno de desarrollo.

### Resultado de tests
- Se ejecutaron los 21 tests de la suite del blog, todos pasaron exitosamente.

---

## 2026-06-10 18:50 — `feat: blog-slug-url`

### Tarea completada
Se eliminó el prefijo `/blog/` de la URL pública de los posts para tener URLs más limpias (ej. `uranodev.test/mi-post`).

### Cambios realizados

**`routes/web.php`** (modificado)
- Se movió la ruta de posts al final del archivo para que actúe como un "catch-all" y se cambió de `/blog/{slug}` a `/{slug}`.

### Resultado de tests
- Se ejecutaron los tests de `PublicBlogPostTest.php` confirmando que el cambio de ruta funciona correctamente y las aserciones de `route('blog.show', ...)` se resuelven a la nueva estructura.

---

## 2026-06-10 18:45 — `fix: blog-public-post-404`

### Tarea completada
Se corrigió el error 404 al acceder a posts públicos y se mejoró la interfaz de administración para facilitar la verificación.

### Cambios realizados

**Dashboard de Posts** (modificado)
- Se añadió un botón "Ver" con icono de ojo en la tabla de posts. Este botón abre la página pública del post en una nueva pestaña, permitiendo una verificación inmediata tras publicar o editar.

**PostStaticGenerationTest** (modificado)
- Se eliminó la limpieza agresiva de `storage/app/public/posts` en el `beforeEach`. Esto evita que el contenido real creado por el usuario desaparezca al ejecutar las pruebas.

**Corrección de datos**
- Se regeneró manualmente vía Tinker el archivo estático para el post "tercer-primer-post" que presentaba el error 404 debido a la falta del archivo físico.

### Resultado de tests
- Tests de `PublicBlogPostTest.php` y `PostStaticGenerationTest.php` pasan correctamente (9 tests).

---

## 2026-06-10 18:36 — `feat: blog-public-post`

### Tarea completada
Se implementó la visualización pública de los posts individuales sirviendo el contenido desde los archivos HTML estáticos generados.

### Cambios realizados

**`app/Http/Controllers/BlogController.php`** (nuevo)
- Controlador encargado de buscar el post por slug y devolver el contenido del archivo HTML almacenado en `storage/app/public/posts/`.
- Implementa validaciones para asegurar que solo se muestren posts con estado `published` y con archivo físico existente.

**`routes/web.php`** (modificado)
- Se añadió la ruta GET `/blog/{slug}` vinculada a `BlogController@show`.

**`tests/Feature/PublicBlogPostTest.php`** (nuevo)
- Tests de feature para verificar la carga correcta del contenido estático, el manejo de 404 para posts no publicados o archivos faltantes.

### Resultado de tests
Tests de `PublicBlogPostTest.php`, `PostStaticGenerationTest.php`, `BlogEditorTest.php` y `PostPublishedAtTest.php` pasan correctamente.

---

## 2026-06-10 18:33 — `fix: blog-published-at`

### Tarea completada
Se corrigió el problema donde `published_at` no se actualizaba automáticamente al cambiar el estado de un post a "published".

### Cambios realizados

**`app/Observers/PostObserver.php`** (modificado)
- Se añadió el método `saving` para interceptar el cambio de estado.
- Si el estado cambia a `published` y `published_at` es nulo, se asigna automáticamente `now()`.

**`tests/Feature/PostPublishedAtTest.php`** (nuevo)
- Se añadieron tests para verificar que `published_at` se asigna correctamente al publicar y que no se sobreescribe si ya tiene un valor.

### Resultado de tests
Tests de `PostPublishedAtTest.php`, `PostStaticGenerationTest.php` y `BlogEditorTest.php` pasan correctamente.

---

## 2026-06-11 00:30 — `fix: blog-editor-validation-final`

### Tarea completada
Solución definitiva al error de desincronización y validación `validation.required` en el editor Markdown.

### Cambios realizados

**`resources/views/components/ui/?markdown-editor.blade.php`** (modificado)
- Se corrigió `@entangle($attributes->wire('model'))` por `@entangle('value')`. Al ser un componente Livewire con `#[Modelable]`, debe entrelazarse con su propia propiedad interna para que la sincronización sea robusta y bidireccional.
- Se simplificó la lógica de sincronización eliminando el despacho de eventos manuales (`input`, `change`) y confiando en la reactividad nativa de Alpine-Livewire a través de la propiedad entrelazada.
- Se añadió una guarda de comparación (`if (this.value !== content)`) para evitar actualizaciones redundantes durante la edición.

### Resultado de tests
5/5 tests de `BlogEditorTest.php` pasan correctamente.

---

## 2026-06-10 19:30 — `fix: blog-editor-validation-revisited`

### Tarea completada
Solucionado definitivamente el error de validación `validation.required` al crear un post desde el dashboard.

### Cambios realizados

**`resources/views/components/ui/?markdown-editor.blade.php`** (modificado)
- Se añadió `.live` al `@entangle` para asegurar que Livewire se mantenga sincronizado en tiempo real con el estado de Alpine.js.
- Se añadió un listener para el evento `blur` en CodeMirror para forzar la sincronización del valor antes de perder el foco, lo cual es crítico al hacer click directamente en el botón de envío.
- Se implementó el despacho de eventos nativos `input` y `change` en el textarea subyacente para mejorar la compatibilidad con el sistema de detección de cambios de Livewire.

### Resultado de tests
Todos los tests de `BlogEditorTest.php` pasaron correctamente.

---

## 2026-06-10 19:10 — `fix: blog-markdown-persistence-v2`

### Tarea completada
Persistencia definitiva del contenido Markdown y corrección del estado del formulario tras guardar.

### Cambios realizados

**`resources/views/components/ui/?markdown-editor.blade.php`** (modificado)
- Se cambió la sincronización del contenido de `blur` a `change` en el editor EasyMDE/CodeMirror para asegurar que Livewire reciba los datos en tiempo real y no se pierdan al enviar el formulario rápidamente.
- Se simplificó la lógica del `$watch` para evitar comparaciones innecesarias y asegurar la reactividad bidireccional limpia.

**`resources/views/pages/posts/?index.blade.php`** (modificado)
- Se restauró la llamada a `$this->reset()` después de guardar un post con éxito. Esto asegura que el estado interno de Livewire se limpie correctamente, evitando que datos antiguos persistan en la siguiente apertura del modal.

**`tests/Feature/BlogEditorTest.php`** (modificado)
- Añadido test `content is cleared after successful save` para verificar que el estado del componente se limpia tras el guardado.

### Resultado de tests
5/5 tests de `BlogEditorTest` pasan.

### Git label
`fix: blog-markdown-persistence-v2`

---

## 2026-06-10 18:35 — `fix: blog-markdown-persistence`

### Tarea completada
Solucionado el problema donde el contenido Markdown desaparecía después de guardar o refrescar el CRUD de posts.

### Cambios realizados

**`app/Observers/PostObserver.php`** (modificado)
- Se cambió el método de actualización del `static_path` dentro del bloque `withoutEvents`. En lugar de `$post->update(['static_path' => $staticPath])`, ahora se asigna el atributo directamente y se llama a `$post->save()`, lo cual es más seguro y consistente dentro de un observador cuando se trabaja con el mismo modelo para evitar pérdida de datos si el modelo estaba sucio.

**`resources/views/pages/posts/?index.blade.php`** (modificado)
- Se comentó la línea que reseteaba todas las propiedades (`reset()`) después de guardar un post. Esto permite que los datos permanezcan en el formulario (si el modal sigue abierto o se reabre) y evita la sensación de pérdida de datos en caso de errores de sincronización o refrescos inmediatos. Se mantiene el reseteo manual de `editingId`.

**`tests/Feature/BlogEditorTest.php`** (modificado)
- Añadido test `content persists after save and edit` para verificar que el contenido se mantiene correctamente después de una edición y guardado, incluso simulando la reapertura del formulario.

### Resultado de tests
Todos los tests de `BlogEditorTest` y `PostStaticGenerationTest` pasan.

### Git label
`fix: blog-markdown-persistence`

---

## 2026-06-10 18:25 — `feat: blog-static`

### Tarea completada
Generación automática de archivos HTML estáticos para los posts al ser publicados o editados.

### Cambios realizados

**`app/Services/PostStaticGenerator.php`** (nuevo)
- Servicio encargado de convertir el contenido Markdown de un post a HTML usando `League\CommonMark`.
- Envuelve el contenido en un layout base con estilos mínimos.
- Gestiona la creación del directorio `storage/app/public/posts`.
- Proporciona métodos para generar y eliminar archivos físicos.

**`app/Observers/PostObserver.php`** (nuevo)
- Observador del modelo `Post`.
- Escucha el evento `saved`: si el estado es `published`, genera (o regenera) el archivo HTML y actualiza la columna `static_path`.
- Maneja el cambio de `slug` eliminando el archivo HTML anterior.
- Escucha el evento `deleted` para limpiar el archivo HTML del almacenamiento.

**`app/Providers/AppServiceProvider.php`** (modificado)
- Registro del `PostObserver` para el modelo `Post`.

**`tests/Feature/PostStaticGenerationTest.php`** (nuevo)
- Tests de integración para verificar:
    - Generación de HTML al publicar.
    - Actualización de HTML al editar post publicado.
    - Eliminación de HTML al borrar post.
    - Eliminación de HTML al despublicar (pasar a draft).
    - Renombrado de archivo al cambiar slug.

### Resultado de tests
4/4 tests de PostStaticGenerationTest pasan.

### Git label
`feat: blog-static`

---

## 2026-06-10 18:15 — `fix: blog-editor-cursor-jump`

### Tarea completada
Solucionado problema de cursor saltarín y saltos de línea en el editor Markdown mediante rollback y reestructuración de la sincronización.

### Cambios realizados

**`resources/views/components/ui/?markdown-editor.blade.php`** (modificado)
- Se eliminó la sincronización inmediata `on('change')` que causaba el bucle de retroalimentación y saltos de cursor.
- Se implementó sincronización en el evento `blur` (perder foco) para asegurar que los datos se guarden antes de enviar el formulario.
- Se añadió un listener `change` minimalista que solo sincroniza si el contenido está vacío (para validación instantánea).
- Uso dinámico de `@entangle($attributes->wire('model'))` para mayor compatibilidad con directivas de Livewire.

### Resultado de tests
10/10 tests de Blog pasan.

### Git label
`fix: blog-editor-cursor-jump`

---

## 2026-06-10 17:59 — `fix: blog-editor-validation`

### Tarea completada
Solucionado error de validación `validation.required` al crear post con el editor Markdown.

### Cambios realizados

**`resources/views/components/ui/?markdown-editor.blade.php`** (modificado)
- Se añadió `.live` a `@entangle('value')` para sincronización inmediata.
- Se añadió evento `blur` a CodeMirror para asegurar el volcado de datos al perder el foco.
- Se activó `forceSync: true` en EasyMDE.

**`resources/views/pages/posts/?index.blade.php`** (modificado)
- Uso de `wire:model.live` al instanciar el componente editor.

**`tests/Feature/BlogEditorTest.php`** (modificado)
- Añadido test de validación para asegurar que el contenido es detectado correctamente por Livewire.

### Resultado de tests
10/10 tests de Blog pasan.

### Git label
`fix: blog-editor-validation`

---

## 2026-06-10 17:51 — `feat: blog-editor`

### Tarea completada
Editor Markdown con soporte de bloques de código (PHP, HTML, JS).

### Cambios realizados

**`resources/views/components/ui/?markdown-editor.blade.php`** (nuevo)
- Componente Livewire (SFC) que integra EasyMDE.
- Soporte para bloques de código con resaltado de sintaxis (opcional en EasyMDE).
- Sincronización bidireccional mediante `@entangle` y `#[Modelable]`.
- Estilos adaptados para modo oscuro y consistencia con Flux UI.

**`resources/views/pages/posts/?index.blade.php`** (modificado)
- Reemplazo de `flux:textarea` por `livewire:ui.markdown-editor` para el campo de contenido del post.

**`tests/Feature/BlogEditorTest.php`** (nuevo)
- Tests para verificar el guardado de bloques de código y la reactividad del componente editor.

### Resultado de tests
9/9 tests de Blog (Crud + Editor) pasan.

### Git label
`feat: blog-editor`

---

## 2026-06-10 18:25 — `feat: blog-crud`

### Tarea completada
CRUD de posts en el dashboard con permisos por rol.

### Cambios realizados

**`app/Policies/PostPolicy.php`** (nuevo/modificado)
- Definición de permisos: Admins pueden gestionar todo. Autores solo sus propios posts.

**`resources/views/pages/posts/?index.blade.php`** (nuevo)
- Componente Livewire (Volt-like MFC) para el CRUD de posts.
- Listado con paginación y filtrado por autor automático.
- Gestión mediante modales Flux UI para crear, editar y eliminar.
- Soporte para campos: título, slug (auto-generado), contenido (markdown), extracto, estado y fecha de publicación.

**`routes/web.php`** (modificado)
- Registro de la ruta `dashboard/posts` asignada al nombre `posts.index`.

**`tests/Feature/BlogCrudTest.php`** (nuevo)
- 7 tests cubriendo: acceso por rol, visibilidad de posts (autor vs admin), creación de posts y edición autorizada.

### Resultado de tests
7/7 tests de BlogCrudTest pasan.

### Git label
`feat: blog-crud`

---

## 2026-06-10 17:55 — `feat: blog-models`

### Tarea completada
Modelos y migraciones: Post, Tag

### Cambios realizados

**`database/migrations/2026_06_10_233947_create_posts_table.php`** (nuevo)
Tabla `posts`: `user_id` (FK), `title`, `slug` (unique), `content` (markdown), `excerpt` (nullable), `cover_image` (nullable), `status` (enum: draft, published, scheduled, archived), `published_at` (nullable), `static_path` (nullable), timestamps.

**`database/migrations/2026_06_10_233948_create_tags_table.php`** (nuevo)
Tabla `tags`: `name` (unique), `slug` (unique), timestamps.

**`database/migrations/2026_06_10_233949_create_post_tag_table.php`** (nuevo)
Tabla pivote `post_tag` con FK a `posts` y `tags` (cascade delete) y restricción unique para el par.

**`app/Models/Post.php`** (nuevo)
- Propiedades fillable y casts definidas.
- Relaciones: `author` (belongsTo User), `tags` (belongsToMany Tag).

**`app/Models/Tag.php`** (nuevo)
- Propiedades fillable definidas.
- Relaciones: `posts` (belongsToMany Post).

**`database/factories/PostFactory.php`** y **`database/factories/TagFactory.php`** (nuevo)
Generación de datos de prueba con slugs automáticos.

**`database/seeders/PostSeeder.php`** (nuevo)
Crea 10 tags y 20 posts vinculados aleatoriamente a tags (1-3 por post).

**`tests/Feature/PostTest.php`** (nuevo)
3 tests: creación de post, asociación con tags, y validación de estados.

### Resultado de tests
3/3 tests de PostTest pasan.

### Git label
`feat: blog-models`

---

## 2026-06-10 — `feat: links-public`

### Cambios realizados

**`routes/web.php`** (modificado)
La ruta `GET /links` ahora carga los links activos de la BD ordenados por `sort_order` y los pasa a la vista. Asignado nombre `links.public`.

**`resources/views/links.blade.php`** (modificado)
Vista dinámica reemplaza los links estáticos hardcodeados:
- Itera los links activos recibidos y genera un `<a>` por cada uno apuntando a `route('links.click', $link)`.
- Muestra mensaje "No hay links disponibles por ahora." cuando la lista está vacía.
- Mantiene el diseño Linktree centrado con el mismo estilo visual.

**`tests/Feature/LinksPublicTest.php`** (nuevo)
6 tests: acceso sin auth, muestra links activos, oculta inactivos, URLs apuntan a ruta de tracking, orden correcto por sort_order, mensaje cuando no hay links.

### Resultado de tests
49/49 pasan (LinkModelTest + LinksCrudTest + LinksTrackingTest + LinksPublicTest).

### Git label
`feat: links-public`

---

## 2026-06-10 — `feat: links-tracking`

### Tarea completada
Conteo y registro de clicks por link en la BD

### Cambios realizados

**`app/Http/Controllers/LinkClickController.php`** (nuevo)
Controlador con método `click(Request $request, Link $link)`:
- Devuelve 404 si el link está inactivo.
- Crea un `LinkClick` con `ip_address` y `user_agent` del request.
- Redirige a `$link->url` o al home si la URL es null (links internos sin post asignado).

**`routes/web.php`** (modificado)
Nueva ruta pública `GET /links/{link}/click` ? `LinkClickController@click`, nombre `links.click`.

**`tests/Feature/LinksTrackingTest.php`** (nuevo)
9 tests: registro en BD, redirección a URL, captura de IP, captura de user-agent, 404 en link inactivo, 404 en link inexistente, redirect a home en link interno sin URL, registro de click en link interno, múltiples clicks contabilizados.

### Resultado de tests
9/9 pasan.

### Git label
`feat: links-tracking`

---

## 2026-06-10 — `feat: links-reorder`

### Tarea completada
Reordenamiento de links (drag & drop u orden manual)

### Cambios realizados

**`resources/views/pages/links/?index.blade.php`** (modificado)
- Eliminado `WithPagination`; computed `links()` ahora usa `get()` en lugar de `paginate(15)` para permitir reordenamiento global.
- `openCreate()`: asigna `sortOrder = (max(sort_order) ?? 0) + 1` para que los links nuevos queden al final automáticamente.
- Método `handleSort(int $id, int $position)` nuevo: recibe el id del link movido y su nueva posición, reordena todos los links secuencialmente y persiste los nuevos `sort_order`.
- Template: columna de drag handle agregada (ícono `bars-3`), `wire:sort="handleSort"` en `flux:table.rows`, `wire:sort:item="{{ $link->id }}"` en cada fila, `wire:sort:handle` en el handle, `wire:sort:ignore` en los botones de acción. Eliminada la columna "Orden" de la tabla. Eliminado campo "Orden" del modal de creación/edición (se gestiona via drag & drop).

**`tests/Feature/LinksCrudTest.php`** (modificado)
Agregados 4 tests nuevos:
- `handleSort mueve un link a una nueva posicion` — arrastrar al inicio.
- `handleSort mueve un link al final` — arrastrar al final.
- `handleSort mueve un link al medio` — arrastrar al centro.
- `al abrir el formulario de crear el orden se asigna automaticamente al final` — verifica sortOrder automático.

### Resultado de tests
22/22 pasan (18 existentes + 4 nuevos).

### Git label
`feat: links-reorder`

---

## 2026-06-10 17:30 (-06:00) `feat: links-crud`
---
Falló el loop, no se actualizó correctamente
Archivos creados/modificados:
- resources/views/pages/links/?index.blade.php — componente Livewire CRUD completo
- routes/web.php — ruta links.index registrada bajo middleware admin
- tests/Feature/LinksCrudTest.php — 18 tests nuevos
- tests/Feature/DashboardLayoutTest.php — test del admin actualizado

Tokens: 2,057,595 tokens, USD 1.1706.
---

## 2026-06-10 17:30 (-06:00) â€” `feat: links-models`

> Iteración 1 · 00:05:39 · tokens: 2,057,595 (in 42 / out 16,314 / cache 2,041,239) · USD 1.1706

### Tarea completada
Modelos y migraciones: Link, LinkClick

### Cambios realizados

**`app/Enums/LinkType.php`** (nuevo)
Enum de string: `External`, `Internal`.

**`database/migrations/2026_06_10_000001_create_links_table.php`** (nuevo)
Tabla `links`: `title`, `url` (nullable), `type` (default `external`), `post_id` (nullable bigint sin FK hasta que exista la tabla posts), `sort_order` (default 0), `is_active` (default true), timestamps.

**`database/migrations/2026_06_10_000002_create_link_clicks_table.php`** (nuevo)
Tabla `link_clicks`: `link_id` (FK con cascade delete), `ip_address` (nullable varchar 45), `user_agent` (nullable text), timestamps.

**`app/Models/Link.php`** (nuevo)
Relaciones: `hasMany LinkClick`. Metodos: `isExternal()`, `isInternal()`. Casts: `type` al enum `LinkType`, `is_active` a boolean.

**`app/Models/LinkClick.php`** (nuevo)
Relaciones: `belongsTo Link`.

**`database/factories/LinkFactory.php`** (nuevo)
Estados: `external()`, `internal()`, `inactive()`.

**`database/factories/LinkClickFactory.php`** (nuevo)
Crea clicks con link por defecto, ip y user agent aleatorios.

**`tests/Feature/LinkModelTest.php`** (nuevo)
12 tests: tipo y estado por defecto, link interno/inactivo, relacion con clicks, cascade delete, relacion inversa, campos opcionales de click, sort_order, post_id para links internos.

### Resultado de tests
12/12 tests pasan.

### Git label sugerido
`feat: links-models`

---
## 2026-06-09 15:05 (-06:00) â€” `fix: ideas-login-redirect`

### Problema
Al hacer click en el link de "Inicia sesiÃ³n" desde la pÃ¡gina pÃºblica de ideas, tras completar el login el usuario era redirigido al home en lugar de regresar a la pÃ¡gina de ideas.

### Causa
Ni los links estÃ¡ticos ni los redirects de Livewire almacenaban la URL de retorno en la sesiÃ³n. Fortify usa `redirect()->intended()` tras el login, pero sin un `url.intended` en sesiÃ³n, caÃ­a al default (`/dashboard`), y el middleware `dashboard.access` redirigÃ­a a los visitantes al home.

### Cambios realizados

**`resources/views/pages/ideas/âš¡public.blade.php`** (modificado)
- Los links de "Inicia sesiÃ³n" y "regÃ­strate" del banner ahora incluyen `?intended=` con la URL de la pÃ¡gina de ideas.
- `toggleVote()` y `suggestIdea()`: almacenan `url.intended` en sesiÃ³n antes de redirigir al login.

**`resources/views/layouts/auth/simple.blade.php`** (modificado)
Bloque `@php` al inicio que lee el query parameter `intended` y lo almacena en `session('url.intended')` si la URL pertenece al mismo dominio (previene open redirect). Aplica a login y registro.

**`tests/Feature/IdeasVotingTest.php`** (modificado)
Test nuevo: `toggleVote` guarda `url.intended` en sesiÃ³n al redirigir al login.

**`tests/Feature/IdeasPublicTest.php`** (modificado)
3 tests nuevos: link de login incluye parÃ¡metro `intended`, pÃ¡gina de login almacena intended en sesiÃ³n, URLs externas son ignoradas (seguridad).

### Resultado de tests
104/104 pasan (toda la suite).

### Git label sugerido
`fix: ideas-login-redirect`

---

## 2026-06-09 09:50 (-06:00) â€” `feat: ideas-public`

### Tarea completada
Pagina publica de ideas ordenadas por votos

### Cambios realizados

**`resources/views/pages/ideas/âš¡public.blade.php`** (modificado)
Componente Livewire publico ampliado con las siguientes funcionalidades:

- **Formulario de sugerencia de ideas:** Los usuarios autenticados y verificados ven un boton "Sugerir una idea" que despliega un formulario inline (titulo + descripcion). Al enviar, la idea se crea con estado `pending` y se muestra un toast de confirmacion. Incluye validacion (titulo requerido max 255, cuerpo requerido max 5000). Cancelar cierra el formulario y resetea campos.
- **Ideas propias del usuario (pendientes/rechazadas):** Seccion "Tus ideas sugeridas" que muestra al usuario autenticado sus ideas en estado `pending` (fondo ambar, badge "Pendiente") y `rejected` (fondo rojo, badge "Rechazada"). Estas ideas solo son visibles para su autor; otros usuarios no las ven.
- **Ordenamiento mejorado:** Ideas aprobadas ordenadas por `votes_count` desc con desempate por `created_at` desc.
- **Banners contextuales:** Texto actualizado para incluir mencion a sugerir ideas (no solo votar). Visitantes no autenticados ven link a login/registro; usuarios sin email verificado ven aviso de verificacion.
- **Protecciones en sugerencia:** `suggestIdea()` redirige al login si no hay sesion, devuelve 403 si el email no esta verificado.
- Propiedades nuevas: `$suggestionTitle`, `$suggestionBody`, `$showSuggestionForm`.
- Computed nuevo: `userIdeas()` retorna ideas del usuario actual con status pending o rejected.

**`tests/Feature/IdeasPublicTest.php`** (nuevo)
14 tests que cubren:
- Ordenamiento por votos descendente (`assertSeeInOrder`).
- Sugerir idea: creacion con estado pending, reseteo de formulario, validacion de campos requeridos.
- Restricciones de sugerencia: redireccion al login si no autenticado, 403 si no verificado.
- Ideas propias: usuario ve sus pendientes y rechazadas, no ve las de otros usuarios.
- Cancelar formulario: cierra y resetea campos.
- Visibilidad del boton: verificado lo ve, no verificado y no autenticado no lo ven.

### Resultado de tests
100/100 tests pasan (toda la suite). 47 tests relacionados con ideas (14 nuevos + 33 previos).

### Git label sugerido
`feat: ideas-public`

---

## 2026-06-09 10:30 â€” `feat: ideas-voting`

### Tarea completada
Sistema de votacion: votar y desvotar por idea (1 voto por visitante)

### Cambios realizados

**`app/Models/Idea.php`** (modificado)
Agrega metodo `hasVotedBy(User $user): bool` â€” verifica si un usuario ya voto por una idea consultando la relacion `votes`.

**`resources/views/pages/ideas/âš¡public.blade.php`** (nuevo)
Componente Livewire publico con layout `components.layouts.app`:
- Lista ideas aprobadas ordenadas por `votes_count` desc (paginadas de 15 en 15).
- Boton de voto por idea: estado visual activo/inactivo segun `votedIdeaIds`.
- `toggleVote(Idea $idea)`: redirige al login si no esta autenticado, devuelve 403 si el email no esta verificado o la idea no esta aprobada. Si ya voto: elimina el `Vote` y hace `decrement('votes_count')`; si no: crea el `Vote` y hace `increment('votes_count')`.
- Computed `ideas()`: query de ideas aprobadas ordenadas por votos.
- Computed `votedIdeaIds()`: pluck de `idea_id` de los votos del usuario autenticado (array vacio si no hay sesion).
- Banner informativo para usuarios no autenticados (links a login/registro) y para usuarios sin email verificado.

**`routes/web.php`** (modificado)
Agrega `Route::livewire('ideas', 'pages::ideas.public')->name('ideas.public')` como ruta publica.

**`resources/views/components/layouts/app.blade.php`** (modificado)
Agrega enlace "Ideas" a la navegacion del layout publico apuntando a `route('ideas.public')`.

**`tests/Feature/IdeasVotingTest.php`** (nuevo)
11 tests: acceso publico sin auth, solo ideas aprobadas visibles, votar crea Vote e incrementa `votes_count`, desvotar elimina Vote y decrementa `votes_count`, redireccion al login si no autenticado, 403 si email no verificado, 403 si idea no aprobada, `votedIdeaIds` correcto para usuario con votos y para usuario no autenticado.

### Resultado de tests
33/33 tests relacionados con ideas pasan (11 nuevos + 22 existentes). Suite completa OK.

### Git label sugerido
`feat: ideas-voting`

---

## 2026-06-09 â€” `feat: ideas-crud`

### Tarea completada
CRUD de ideas en el dashboard con flujo de aprobacion (solo admin)

### Cambios realizados

**`app/Http/Middleware/EnsureIsAdmin.php`** (nuevo)
Middleware que devuelve 403 si el usuario no es administrador.

**`bootstrap/app.php`** (modificado)
Registra el alias `admin` para `EnsureIsAdmin`.

**`routes/web.php`** (modificado)
Agrega ruta `ideas.index` protegida con `auth + verified + dashboard.access + admin`, usando `Route::livewire('dashboard/ideas', 'pages::ideas.index')`.

**`resources/views/pages/ideas/âš¡index.blade.php`** (nuevo)
Componente Livewire de pagina completa con CRUD completo:
- Tabla paginada con columnas: TÃ­tulo, Estado (badge color), Votos, Autor, Fecha, Acciones.
- Boton "Nueva idea" abre modal de creacion.
- Acciones por fila: Aprobar y Rechazar (solo ideas pendientes, con `wire:confirm`), Editar (abre modal con datos cargados), Eliminar (abre modal de confirmacion).
- Modal `idea-form`: crea o edita segun `$editingId`. Valida titulo (max 255) y cuerpo (max 5000).
- Modal `delete-idea`: confirmacion antes de eliminar.

**`tests/Feature/IdeasCrudTest.php`** (nuevo)
11 tests: acceso de admin/author/visitor/invitado, crear con validacion, editar, aprobar, rechazar, eliminar, listado visible.

**`tests/Feature/DashboardLayoutTest.php`** (modificado)
Actualizado el test del admin: sustituye `data-nav="ideas"` por `assertSee('Ideas')` ya que la ruta ahora existe y el sidebar muestra el link real.

### Resultado de tests
75/75 pasan (toda la suite).

### Git label sugerido
`feat: ideas-crud`

---

## 2026-06-09 08:45 â€” `feat: ideas-models`

### Tarea completada
Modelos y migraciones: Idea, Vote

### Cambios realizados

**`app/Enums/IdeaStatus.php`** (nuevo)
Enum de string: `Pending`, `Approved`, `Rejected`.

**`database/migrations/..._create_ideas_table.php`** (nuevo)
Tabla `ideas`: `user_id` (FK), `title`, `body`, `status` (default `pending`), `votes_count` (default 0), timestamps.

**`database/migrations/..._create_votes_table.php`** (nuevo)
Tabla `votes`: `user_id` (FK), `idea_id` (FK), timestamps. Restriccion unique en `(user_id, idea_id)` para garantizar 1 voto por usuario por idea.

**`app/Models/Idea.php`** (nuevo)
Relaciones: `belongsTo User`, `hasMany Vote`. Metodos: `isPending()`, `isApproved()`, `isRejected()`, `approve()`, `reject()`. Cast de `status` al enum `IdeaStatus`.

**`app/Models/Vote.php`** (nuevo)
Relaciones: `belongsTo User`, `belongsTo Idea`.

**`app/Models/User.php`** (modificado)
Agrega relaciones `hasMany Ideas` y `hasMany Votes`.

**`database/factories/IdeaFactory.php`** (nuevo)
Estados: `pending()`, `approved()`, `rejected()`.

**`database/factories/VoteFactory.php`** (nuevo)
Crea votos con usuario y idea aprobada por defecto.

**`tests/Feature/IdeaModelTest.php`** (nuevo)
11 tests: estados por defecto, approve/reject, relaciones (userâ†’ideas, ideaâ†’votes, userâ†’votes), unicidad del voto, votes_count inicial.

### Resultado de tests
64/64 pasan (toda la suite).

### Git label sugerido
`feat: ideas-models`

---

## 2026-06-09 â€” `fix: visitor-logout`

### Problema
Los visitantes autenticados eran redirigidos al home desde el dashboard pero no tenian forma de cerrar sesiÃ³n ni acceso al dashboard, quedando "atrapados" en la sesiÃ³n.

### Solucion
**`resources/views/components/layouts/app.blade.php`** (modificado)
El header del layout pÃºblico ahora adapta la zona de acciones segÃºn el estado de sesiÃ³n:
- **Sin sesiÃ³n:** link "Iniciar sesiÃ³n" + botÃ³n "Get Started".
- **Visitor logueado:** nombre del usuario + botÃ³n "Cerrar sesiÃ³n".
- **Author o Admin logueado:** link "Dashboard" + botÃ³n "Cerrar sesiÃ³n".

---

## 2026-06-09 â€” `feat: dashboard-layout`

### Tarea completada
Layout del dashboard con navegacion adaptada por rol

### Cambios realizados

**`app/Http/Middleware/EnsureHasDashboardAccess.php`** (nuevo)
Middleware que redirige a los visitantes al home si intentan acceder al dashboard. Solo autores y administradores tienen acceso.

**`bootstrap/app.php`** (modificado)
Registra el alias `dashboard.access` para el nuevo middleware.

**`routes/web.php`** (modificado)
Agrega `dashboard.access` al grupo de middleware del dashboard.

**`resources/views/layouts/app/sidebar.blade.php`** (modificado)
NavegaciÃ³n adaptada por rol:
- Todos (author + admin): secciÃ³n "Plataforma" con Dashboard.
- Author + Admin: secciÃ³n "Contenido" con Posts.
- Admin Ãºnicamente: secciÃ³n "AdministraciÃ³n" con Ideas, Links y Usuarios.
Los items futuros (Posts, Ideas, Links, Usuarios) usan `data-nav` como identificador y href="#" mientras no existan las rutas; cuando las rutas se aÃ±adan se conectan automÃ¡ticamente via `Route::has()`.
Eliminados los links a Repositorio y DocumentaciÃ³n (no relevantes para el proyecto).

**`tests/Feature/DashboardLayoutTest.php`** (nuevo)
7 tests verifican: redirect de visitantes, acceso de autores y admins, visibilidad de secciones por rol.

**`tests/Feature/DashboardTest.php`** (modificado)
Actualizado para usar `User::factory()->author()` en lugar del factory default (visitor), que ahora es redirigido.

### Resultado de tests
53/53 pasan (toda la suite).

### Git label sugerido
`feat: dashboard-layout`

---

## 2026-06-09 â€” `feat: google-oauth`

### Tarea completada
Registro e inicio de sesion con Google OAuth

### Cambios realizados

**`composer.json`** (modificado)
Instalado `laravel/socialite ^5.27`.

**`database/migrations/2026_06_09_081335_add_google_id_to_users_table.php`** (nuevo)
- Agrega columna `google_id` (nullable, unique) a la tabla `users`.
- Hace `password` nullable para soportar usuarios que solo usan Google.

**`config/services.php`** (modificado)
Agrega configuracion de Google con `client_id`, `client_secret` y `redirect` desde variables de entorno.

**`.env.example`** (modificado)
Agrega `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`.

**`app/Models/User.php`** (modificado)
Agrega `google_id` y `email_verified_at` a `#[Fillable]`.

**`app/Http/Controllers/OAuthController.php`** (nuevo)
Dos metodos:
- `redirectToGoogle()`: redirige al proveedor OAuth de Google.
- `handleGoogleCallback()`: busca usuario por `google_id` o `email`; si existe lo vincula, si no lo crea con rol `visitor` y email verificado. Hace login con `remember: true`.

**`routes/web.php`** (modificado)
Rutas `auth.google.redirect` y `auth.google.callback` dentro del prefijo `/auth/google`.

**`resources/views/pages/auth/login.blade.php`** (modificado)
Boton "Continuar con Google" con icono SVG oficial, separador y estilos Tailwind.

**`resources/views/pages/auth/register.blade.php`** (modificado)
Boton "Registrarse con Google" con el mismo diseÃ±o.

**`tests/Feature/GoogleOAuthTest.php`** (nuevo)
5 tests: redirect, nuevo usuario, vinculacion por email, relogin con google_id existente, y verificacion de email en usuario previo sin verificar.

### Resultado de tests
46/46 pasan (toda la suite).

### Git label sugerido
`feat: google-oauth`

---

## 2026-06-09 â€” `feat: auth`

### Tarea completada
Autenticacion: registro, login, logout, recuperar password, verificacion de email

### Contexto
Las vistas, rutas y tests de autenticacion ya existian (Fortify + Flux UI). El unico elemento faltante era que el modelo `User` no implementaba `MustVerifyEmail`, por lo que Fortify nunca enviaba el correo de verificacion al registrarse.

### Cambios realizados

**`app/Models/User.php`** (modificado)
Implementa `Illuminate\Contracts\Auth\MustVerifyEmail`. Ahora Fortify envia el email de verificacion al registrar un nuevo usuario y el middleware `verified` bloquea el acceso al dashboard hasta verificar.

**`app/Actions/Fortify/CreateNewUser.php`** (modificado)
Asigna `role => Role::Visitor` explicitamente al crear un nuevo usuario.

**`tests/Feature/Auth/RegistrationTest.php`** (modificado)
Agregados dos tests nuevos:
- Verifica que nuevos usuarios se registran con rol `visitor`.
- Verifica que usuarios no verificados son redirigidos a la pagina de verificacion de email al intentar acceder al dashboard.

### Resultado de tests
23/23 tests de Auth pasan.

### Git label sugerido
`feat: auth`

---

## 2026-06-09 â€” `fix: tailwind-theme`

### Problema
CSS sin efecto en el navegador. Vite dev server lanzaba el error:
`Cannot apply unknown utility class 'text-frost-dark'`

### Causa
Las variables `--color-frost-dark`, `--color-frost-light`, `--color-frost-muted`, `--color-frost-border` y los `--spacing-fluid-*` estaban definidas dentro de `@layer theme { }` (capa CSS estÃ¡ndar), en lugar de `@theme { }` (directiva especial de Tailwind v4). Solo las variables en `@theme` generan clases utilitarias como `text-frost-dark`, `bg-frost-dark`, etc.

### Solucion
**`resources/css/app.css`** (modificado)
Movidas todas las variables custom al bloque `@theme`. El `@layer theme` quedo solo con la sobreescritura del modo oscuro (`.dark { ... }`).

---

## 2026-06-09 â€” `chore: setup` + `feat: roles`

### Tareas completadas
- Estructura base: migraciones de usuarios y configuracion inicial
- Sistema de roles: visitor, author, admin

### Cambios realizados

**`app/Enums/Role.php`** (nuevo)
Enum de string con tres casos: `Visitor`, `Author`, `Admin`.

**`database/migrations/2026_06_09_074634_add_role_to_users_table.php`** (nuevo)
Agrega columna `role` (string) a la tabla `users`, con valor por defecto `visitor`.

**`app/Models/User.php`** (modificado)
- `role` agregado a `#[Fillable]`
- Cast de `role` al enum `Role`
- Metodos helpers: `isAdmin()`, `isAuthor()`, `isVisitor()`

**`database/factories/UserFactory.php`** (modificado)
- Default `role` â†’ `Role::Visitor`
- Estados nuevos: `admin()`, `author()`, `visitor()`

**`database/seeders/DatabaseSeeder.php`** (modificado)
Crea un usuario admin inicial (`admin@uranodev.com`) al ejecutar `db:seed`.

**`tests/Feature/UserRoleTest.php`** (nuevo)
6 tests que verifican: rol por defecto, roles por estado de factory, persistencia en BD y cambio de rol. Todos pasan.

### Git label sugerido
`chore: setup` + `feat: roles`


## [2026-06-10 19:40] - Fix: Sincronización del cursor en el editor Markdown (Re-fix v2)
- Se aplicó una solución más robusta para evitar el salto del cursor en el editor EasyMDE.
- Se implementó un bloqueo de actualización en el sentido Livewire -> Editor cuando el editor tiene el foco (`hasFocus()`). Esto garantiza que mientras el usuario escribe, Livewire no pueda inyectar contenido que mueva el cursor.
- Se limpiaron las cachés de vistas y configuración para asegurar que los cambios en los componentes Blade se apliquen correctamente.
- Los tests de persistencia y validación siguen pasando.

---

## 2026-06-11 21:15 — ix: links-intern + ix: links-owner + ix: menu-links

### Tareas completadas
Se completo de forma exitosa el resto de las tareas del grupo de Mejoras pendientes, logrando el correcto funcionamiento del editor de Links y el acceso a la navegacion responsiva:
1. **Error validation.required en seleccion de Post interno (fix: links-intern)**: Se reparo la sincronizacion de valor para la seleccion de posts en links internos que fallaba silenciosamente y causaba un error de validacion. 
2. **Owner de Link no guardaba en BD (fix: links-owner)**: Se soluciono el problema en donde la seleccion del administrador propietario (Owner) de un link parecia funcionar visualmente pero al crear/guardar el link se persistia como null en la base de datos.
3. **El menu hamburguesa no funciona en celular (fix: menu-links)**: Se habilito el funcionamiento del menu de navegacion movil (hamburguesa) en dispositivos pequenos en todas las paginas estaticas del sitio publico.

### Cambios realizados

**resources/views/pages/links/?index.blade.php** (modificado)
- En el modal de links, se reemplazo el atributo :value por alue tradicional interpolado en los elementos <flux:select.option> para los campos de asignacion de Posts (postId) y Propietario (userId).
- Se agrego explicitamente el modificador .live a los directivos de modelo (wire:model.live=postId y wire:model.live=userId) para garantizar que la seleccion sincronice los datos inmediatamente y el componente Livewire reconozca la seleccion de forma transparente.

**resources/views/components/layouts/app.blade.php** (modificado)
- Las paginas de frontend publico estaticas que no contienen componentes interactivos nativos de Livewire carecian del bloque @livewireScripts. Esto provocaba que Alpine.js (vital para el menu tipo hamburguesa con x-data) nunca se cargara en esos dispositivos moviles.
- Se agregaron los directivos @livewireStyles y @livewireScripts, forzando al layout a incluir Alpine.js y habilitando asi el despliegue del menu movil responsivo.

### Resultado de tests
- Se verificaron localmente los renders de HTML y la ejecucion de la suite (206/206 tests).