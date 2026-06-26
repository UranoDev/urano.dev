# Sistema para sitio personal Urano Dev

## PHP
El ejecutable de PHP está en: c:/laragon/bin/php/php-8.4.11-nts-Win32-vs17-x64/php.exe
Otras versiones de PHP están en c:/laragon/bin/php/*

## Modo de trabajo

Trabajar en modo plan antes de implementar cambios.
No generar codigo hasta que las tareas esten definidas y aprobadas.

---

## Lineamientos generales

- Todo el codigo, nombres de clases, metodos, variables, migraciones, rutas internas y componentes tecnicos deben generarse en ingles.
- Todos los textos visibles para usuarios, leyendas, etiquetas, mensajes de validacion y contenido de interfaz deben generarse en espanol.
- La aplicacion debe construirse de forma modular, permitiendo enviar a produccion cada tarea terminada sin esperar a completar todo el sistema.
- Los URLs deben ser limpios y legibles, y pueden ser en espanol o en ingles.

---

## Objetivo del sistema

### Blog

Una aplicacion para administrar y publicar posts estilo blog.

**Edicion:**
- Se usara Markdown para editar el contenido del post.
- Los posts podran contener pequenos segmentos de codigo (PHP, HTML, JS).
- Cada post tendra una imagen de portada.
- Cada post tendra etiquetas (tags) escritas libremente con autocompletado de etiquetas ya existentes. Las etiquetas se pueden eliminar del post.

**Publicacion y archivos estaticos:**
- Al publicar un post se genera un archivo HTML estatico en `storage/posts/`.
- Las imagenes del post se almacenan en `storage/images/`.
- Cuando se edita un post ya publicado, el archivo HTML estatico se regenera automaticamente al guardar.
- Cada post tiene un slug editable. Por default se genera automaticamente al guardar por primera vez usando el titulo del post.

**Estados del post:**
- `draft` — borrador, no visible publicamente.
- `published` — publicado y visible en el sitio.
- `scheduled` — por publicar. Tiene un campo de fecha/hora de publicacion. Por default es el momento de creacion, pero se puede modificar a una fecha futura. El scheduler de Laravel cambia el estado a `published` cuando llega la fecha/hora programada y genera el HTML estatico.
- `archived` — archivado, ya no se muestra en el sitio.

**Permisos:**
- Los autores pueden crear posts y editar solo sus propios posts. En el CRUD solo ven sus propios posts.
- Los administradores pueden crear y editar cualquier post. En el CRUD ven todos los posts.

**Pagina publica del blog:**
- Hay una pagina de listado publico del blog que muestra un extracto de cada post y los ordena por fecha de publicacion descendente (mas recientes primero).
- Cada post tiene su propia pagina individual servida desde el archivo HTML estatico generado.

---

### Links

Una pagina publica estilo Linktree con todos los links del perfil.

- La pagina es publica y dinamica (los links se extraen de la BD).
- Solo una pagina con todos los links en lista plana.
- Los links se pueden reordenar (drag & drop u orden manual).
- Los links pueden ser externos (URL libre) o internos (apuntando a un post publicado; se selecciona desde un SELECT HTML con la lista de posts disponibles; la URL usada es la del archivo estatico generado).
- Al hacer click en un link, el visitante es redirigido al destino.
- Cada click se contabiliza y se guarda en la BD.
- Solo los administradores pueden gestionar el CRUD de links.

---

### Ideas

Una pagina publica con lista de ideas votables por los visitantes, ordenada por votos de forma descendente.

**Comportamiento publico:**
- Los visitantes pueden ver todas las ideas aprobadas.
- Para votar o sugerir ideas, el visitante debe estar registrado y verificado.
- Cada visitante puede dar 1 voto por idea.
- Un visitante puede "desvotar" una idea que ya habia votado (elimina el voto).
- Los visitantes pueden sugerir ideas nuevas (requieren aprobacion antes de aparecer en la lista publica).
- En la misma pagina de ideas, se puede sugerir una idea nueva.
- Si el visitante esta registrado y verificado, puede votar o sugerir ideas, si no, solo mostrar un link para registrarse con un copy de sugerir una nueva idea.

**Flujo de aprobacion de ideas sugeridas:**
- Las ideas sugeridas por visitantes quedan en estado `pending` (pendiente).
- Un administrador puede aprobarlas (pasan a `approved` y aparecen publicamente) o rechazarlas (pasan a estado `rejected` y quedan registradas en la BD).
- Si una idea esta en estado `pending` o `rejected`, solo el autor de la sugerencia puede verla, con un diseno diferente que indique claramente que no esta aprobada, y diferente si fue rechazada.

**Estados de una idea:**
- `pending` — sugerida por visitante, esperando revision del administrador.
- `approved` — aprobada, visible publicamente.
- `rejected` — rechazada, no visible publicamente, pero registrada en BD. Solo visible para su autor.

**Permisos:**
- Solo los administradores tienen acceso al CRUD de ideas en el dashboard.
- Los administradores pueden crear, editar, aprobar y rechazar ideas.

---

## Roles y permisos

### Visitante (rol: `visitor`)
- Acceso a paginas publicas (blog, links, ideas).
- Puede registrarse, iniciar sesion, cerrar sesion y recuperar password.
- La sesion de los visitantes no tiene expiracion.
- Al registrarse se le asigna el rol `visitor`.
- Al registrarse se envia un correo de verificacion; hasta verificar su email no puede votar ni sugerir ideas.
- Los usuarios que se registran con Google OAuth quedan automaticamente verificados.
- Puede votar por ideas (1 voto por idea, puede desvotar).
- Puede sugerir ideas nuevas (quedan en estado `pending`).
- No tiene pagina de perfil en esta primera fase.

### Autor (rol: `author`)
- Acceso al dashboard.
- Puede crear posts y editar sus propios posts.
- En el CRUD de posts solo ve sus propios posts.
- No tiene acceso a CRUD de links ni CRUD de ideas.
- Tiene acceso a su perfil de usuario: nombre, foto o avatar, bio y cambio de password.
- La sesion expira con el tiempo estandar de Laravel.

### Administrador (rol: `admin`)
- Acceso completo al dashboard.
- Puede crear y editar cualquier post (ve todos los posts en el CRUD).
- Gestiona el CRUD de links.
- Gestiona el CRUD de ideas (aprobar, rechazar, crear, editar).
- Gestiona el CRUD de autores (crear, editar, desactivar autores).
- Puede gestionar otros administradores.
- La sesion expira con el tiempo estandar de Laravel.

---

## Autenticacion y registro

- Flujo completo: registro, login, logout, recuperacion de password, verificacion de email.
- El email de verificacion se envia al registrarse con email/password.
- Tambien se puede registrar e iniciar sesion con Google OAuth; en este caso el usuario queda automaticamente verificado.
- Los visitantes no verificados no pueden votar ni sugerir ideas.

---

## Almacenamiento

- Archivos HTML estaticos de posts: `storage/posts/`
- Imagenes de posts: `storage/images/`
