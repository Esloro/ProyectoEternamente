# Plantilla del backend Wedding Planner

Esta carpeta contiene los **archivos personalizados** del proyecto (modelos, migraciones, seeders, configuración de entorno) listos para aplicarse sobre un esqueleto de Laravel 11 recién creado.

Se trabaja así porque, en este momento, Docker Hub no es accesible desde tu red y no se puede ejecutar `composer create-project` para inicializar Laravel. Mientras tanto, tener todo el contenido específico del Wedding Planner ya escrito permite aplicarlo en segundos cuando la red vuelva.

---

## Cómo aplicarla (cuando Docker funcione)

```bash
# 1. Asegúrate de que la red Docker funciona
docker pull php:8.3-cli       # debe completarse sin errores
docker pull mailhog/mailhog
docker pull phpmyadmin:5
docker pull node:22-alpine

# 2. Construye la imagen del backend
cd C:\Users\esme_\Documents\ProyectoIntermodular
docker compose build backend

# 3. Inicializa Laravel 11 dentro del contenedor (lo crea en /tmp y lo
#    copia a /var/www/html — que está bind-mounted a ./backend en el host)
docker compose run --rm --no-deps backend bash -c "
  cd /tmp &&
  composer create-project laravel/laravel laravel-base '^11.0' --prefer-dist --no-interaction &&
  cp -a /tmp/laravel-base/. /var/www/html/ &&
  cd /var/www/html &&
  composer require laravel/sanctum &&
  php artisan vendor:publish --provider='Laravel\\Sanctum\\SanctumServiceProvider' &&
  rm -f database/migrations/0001_01_01_000000_create_users_table.php &&
  rm -f database/migrations/0001_01_01_000001_create_cache_table.php &&
  rm -f database/migrations/0001_01_01_000002_create_jobs_table.php
"

# 4. Aplica la plantilla (sobrescribe los archivos que necesitemos en español)
cp -a backend/plantilla/. backend/

# 5. Genera APP_KEY y arranca todo
docker compose up -d
docker compose exec backend php artisan key:generate
docker compose exec backend php artisan migrate --seed
```

Tras esto, la BD `wedding_planner` tendrá todas las tablas y datos de prueba, y la API estará accesible en `http://localhost:8000/api`.

---

## Contenido de la plantilla

```
plantilla/
├── README-PLANTILLA.md            (este archivo)
├── .env.example                   (variables de entorno para Laravel)
├── app/
│   └── Models/                    (10 modelos Eloquent en español)
│       ├── Usuario.php
│       ├── Boda.php
│       ├── Proveedor.php
│       ├── Invitado.php
│       ├── Mesa.php
│       ├── Mensaje.php
│       ├── Paquete.php
│       ├── Portfolio.php
│       ├── Testimonio.php
│       └── MensajeContacto.php
└── database/
    ├── migrations/                (11 migraciones de las tablas del proyecto)
    └── seeders/                   (datos de prueba realistas)
```

---

## Credenciales de los datos sembrados

| Tipo          | Email                          | Contraseña     | Estado de la boda  |
| ------------- | ------------------------------ | -------------- | ------------------ |
| Administrador | `admin@weddingplanner.com`     | `admin1234`    | —                  |
| Cliente 1     | `lucia@example.com`            | `cliente1234`  | `pendiente_reunion`|
| Cliente 2     | `carlos@example.com`           | `cliente1234`  | `activa`           |

---

## Notas técnicas

- **Columna `email_verificado_en`**: el modelo `Usuario` sobrescribe los métodos `hasVerifiedEmail()` y `markEmailAsVerified()` del trait `MustVerifyEmail` para usar la columna en español.
- **ENUM sin tildes**: las columnas ENUM (`franja_horaria`, etc.) usan valores ASCII (`manana`, `tarde`, `noche`) para evitar problemas de codificación. La capa de presentación (Angular) puede mostrar los valores con tildes.
- **Foreign keys**: todas las relaciones están con `cascadeOnDelete()` (al borrar una boda se borran sus invitados/mesas/mensajes) o `nullOnDelete()` (al borrar una mesa, sus invitados quedan sin mesa).
