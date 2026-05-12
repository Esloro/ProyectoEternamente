# =====================================================================
# Backend - Laravel 11 sobre PHP 8.3
# Imagen pensada para desarrollo: usa el servidor embebido de Laravel
# (`php artisan serve`). Para producción se recomendaría php-fpm + nginx,
# pero para el TFG mantenemos la simplicidad.
# =====================================================================

FROM php:8.3-cli

# Dependencias del sistema necesarias para las extensiones de PHP que
# usa Laravel (gd para imágenes, zip para Composer, mysql client, etc.).
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        curl \
        unzip \
        zip \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Extensiones de PHP requeridas por Laravel 11 + Sanctum + envío de emails.
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        opcache

# Configuración de OPcache para acelerar las peticiones al servidor de desarrollo.
# Sin OPcache PHP recompila cada archivo PHP en cada petición, lo que en Docker
# con bind-mount en Windows puede provocar tiempos de 8-10 s por request.
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.validate_timestamps=1'; \
    echo 'opcache.save_comments=1'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Pre-instalamos las dependencias de Composer dentro de la imagen.
# Esto evita que el vendor/ se sirva desde el bind-mount de Windows
# (que es lento), ya que el volumen anónimo de docker-compose.yml
# tiene prioridad sobre el directorio montado del host.
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copiamos el resto del codigo de la aplicacion.
# En desarrollo el bind-mount de docker-compose.yml sobreescribe estos
# archivos con los del host (hot-reload). En produccion (docker-compose.prod.yml)
# no hay bind-mount, asi que esta es la unica fuente de codigo dentro del contenedor.
COPY . .

EXPOSE 8000

CMD ["sh", "-c", "\
    if [ ! -f .env ]; then cp .env.example .env && php artisan key:generate; fi && \
    php artisan serve --host=0.0.0.0 --port=8000"]
