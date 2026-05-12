#!/bin/sh
###############################################################################
# Script de arranque del backend Laravel en PRODUCCION.
# Lo invoca docker-compose.prod.yml como CMD del contenedor backend.
#
# Pasos:
#   1. Espera a que MySQL responda (el healthcheck no garantiza que las
#      tablas iniciales esten creadas si es el primer arranque).
#   2. Ejecuta migraciones (--force es obligatorio en produccion).
#   3. Cachea config, rutas y eventos para mejor rendimiento.
#   4. Arranca el servidor PHP en el puerto 8000.
#
# Notas:
#   - Para un TFG con bajo trafico, `php artisan serve` con OPcache y
#     PHP_CLI_SERVER_WORKERS es suficiente. Para un proyecto real se
#     recomendaria php-fpm + nginx o FrankenPHP/Octane.
###############################################################################

set -e

echo ">> Esperando a MySQL..."
until php -r "new PDO('mysql:host=$DB_HOST;port=$DB_PORT', '$DB_USERNAME', '$DB_PASSWORD');" 2>/dev/null; do
    sleep 2
done
echo ">> MySQL disponible."

if [ -z "$(grep '^APP_KEY=base64:' .env 2>/dev/null)" ]; then
    echo ">> Generando APP_KEY..."
    php artisan key:generate --force
fi

echo ">> Ejecutando migraciones..."
php artisan migrate --force

echo ">> Asegurando symlink public/storage..."
php artisan storage:link --force

echo ">> Cacheando configuracion..."
php artisan config:cache
php artisan route:cache
php artisan event:cache

echo ">> Arrancando servidor en :8000..."
exec php artisan serve --host=0.0.0.0 --port=8000
