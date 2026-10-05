#!/bin/sh
#
# Despliegue de urano.dev, por SSH en la carpeta del sitio en Plesk:
#
#     sh scripts/deploy.sh
#
# Jala la rama de GitHub al repositorio que ya vive en el servidor y corre lo
# que hay que hacer después: dependencias, build, migraciones, portafolio y
# cachés. Vive en el repo y no en el panel porque cambia con el código.
#
# La primera vez el servidor todavía no tiene este archivo: un `git pull`
# a mano lo trae, y de ahí en adelante el script se jala solo.
#
set -e

# Todo va dentro de un bloque `{ ... }` que termina en `exit`: sh lee un script
# poco a poco, y si el `git pull` de abajo lo reescribe mientras corre, seguiría
# leyendo en una posición que ya no corresponde. Un bloque se lee completo antes
# de ejecutarse.
{
    REMOTO="${REMOTO:-origin}"
    RAMA="${RAMA:-master}"

    cd "$(dirname "$0")/.."
    RAIZ="$(pwd)"

    # Plesk no carga el perfil del usuario en un shell no interactivo, así que
    # puede faltar phpenv y nodenv. Se agregan los shims —no las rutas de una
    # versión—: un shim decide qué versión usar y sigue siendo el correcto
    # después de actualizar PHP o Node. Según el servidor viven en el HOME o
    # junto a la carpeta del sitio.
    export PATH="$HOME/.phpenv/shims:$HOME/.nodenv/shims:$(dirname "$RAIZ")/.phpenv/shims:$(dirname "$RAIZ")/.nodenv/shims:$PATH"

    # Si falta algo, el despliegue se detiene: saltarse el build lo reportaría
    # como exitoso mientras el sitio sigue sirviendo los estilos anteriores.
    for programa in git php composer npm; do
        command -v "$programa" >/dev/null 2>&1 || {
            echo "✗ No se encontró '$programa' en el PATH." >&2
            exit 1
        }
    done

    echo "→ Código ($REMOTO/$RAMA)"
    # --ff-only: si el servidor tiene cambios propios que no vienen de GitHub,
    # se detiene en vez de mezclarlos en silencio.
    git pull --ff-only "$REMOTO" "$RAMA"

    echo "→ Dependencias de PHP"
    composer install --no-dev --optimize-autoloader

    echo "→ Dependencias de JavaScript"
    npm ci

    echo "→ Hoja de estilos y scripts"
    npm run build

    echo "→ Migraciones"
    php artisan migrate --force

    # El portafolio vive en PortfolioProjectSeeder, así que publicar un cambio
    # ahí es correr este seeder. Es idempotente y solo toca las tablas del
    # portafolio; nunca `db:seed` a secas, que además crea los usuarios de
    # prueba de DatabaseSeeder.
    echo "→ Portafolio"
    php artisan db:seed --class=PortfolioProjectSeeder --force

    echo "→ Cachés"
    php artisan route:clear
    php artisan view:clear
    php artisan config:clear

    # El servidor no muestra versión en ningún lado: el commit publicado es la
    # forma de confirmar que este despliegue sí aterrizó.
    echo "→ Publicado: $(git log --oneline -1)"
    echo "Listo."
    exit 0
}
