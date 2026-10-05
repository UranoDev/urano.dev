#!/bin/sh
#
# Lo que hay que hacer después de que Plesk copia los archivos al sitio.
#
# Vive aquí y no en la caja de «additional deployment actions» del panel: cambia
# con el código —una migración nueva, un paso nuevo— y así viaja con él. En el
# panel va una sola línea:
#
#     sh <carpeta del sitio>/scripts/deploy.sh
#
set -e

cd "$(dirname "$0")/.."
RAIZ="$(pwd)"

# Plesk corre esto en un shell que no carga el perfil del usuario, así que no
# trae phpenv ni nodenv. Se agregan los shims —no las rutas de una versión—: un
# shim decide qué versión usar y sigue siendo el correcto después de actualizar
# PHP o Node. Según el servidor viven en el HOME o junto a la carpeta del sitio.
export PATH="$HOME/.phpenv/shims:$HOME/.nodenv/shims:$(dirname "$RAIZ")/.phpenv/shims:$(dirname "$RAIZ")/.nodenv/shims:$PATH"

# Si falta algo, el despliegue se detiene: saltarse el build lo reportaría como
# exitoso mientras el sitio sigue sirviendo los estilos de la versión anterior.
for programa in php composer npm; do
    command -v "$programa" >/dev/null 2>&1 || {
        echo "✗ No se encontró '$programa' en el PATH del despliegue." >&2
        exit 1
    }
done

echo "→ Dependencias de PHP"
composer install --no-dev --optimize-autoloader

echo "→ Dependencias de JavaScript"
npm ci

echo "→ Hoja de estilos y scripts"
npm run build

echo "→ Migraciones"
php artisan migrate --force

# El portafolio vive en PortfolioProjectSeeder, así que publicar un cambio ahí es
# correr este seeder. Es idempotente y solo toca las tablas del portafolio; nunca
# `db:seed` a secas, que además crea los usuarios de prueba de DatabaseSeeder.
echo "→ Portafolio"
php artisan db:seed --class=PortfolioProjectSeeder --force

echo "→ Cachés"
php artisan route:clear
php artisan view:clear
php artisan config:clear

echo "Listo."
