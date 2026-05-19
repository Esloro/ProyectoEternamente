/********************* @Esmeralda López Rodero ********************/
# ETERNAMENTE - Trabajo de Fin de Grado de 2º DAW.

Trabajo de Fin de Grado de 2º DAW.
Se trata de una aplicación web completa para una agencia de organización de bodas. Su finalidad es digitalizar 
y centralizar la relación entre la pareja y la coordinadora e integrar todas las herramientas en una misma web
sencilla e intuitiva.

---

## Stack

| Capa            | Tecnología                                    |
| --------------- | --------------------------------------------- |
| Frontend        | Angular 21 (standalone components, SCSS)      |
| Backend         | Laravel 11 + Sanctum (API REST)               |
| Base de datos   | MySQL 8                                       |
| Email (dev)     | Mailhog                                       |
| Contenedores    | Docker + Docker Compose                       |

---

## Requisitos previos

- **Docker Desktop** 24+ (probado con 28.5.1) con Docker Compose v2.
- ~3 GB libres para imágenes y volúmenes.
- Puertos libres en el host: `4200`, `8000`, `3306`, `8080`, `8025`, `1025`.

No hace falta tener PHP, Composer, Node ni Angular CLI instalados en el host: todo se ejecuta dentro de los contenedores.

---

## Servicios y URLs

| Servicio    | URL / acceso                          | Notas                            |
| ----------- | ------------------------------------- | -------------------------------- |
| Frontend    | http://localhost:4200                 | Angular con hot reload           |
| Backend API | http://localhost:8000/api             | Laravel + Sanctum                |
| phpMyAdmin  | http://localhost:8080                 | Usuario `root` / `rootpass`      |
| Mailhog     | http://localhost:8025                 | Bandeja de emails de desarrollo  |
| MySQL       | localhost:3306                        | BD `wedding_planner`             |

Credenciales de la BD (definidas en `docker-compose.yml`):

- Usuario: `wedding_user` / `wedding_pass`
- Root: `root` / `rootpass`
- Base de datos: `wedding_planner`

---

## Arranque rápido

```bash
# 1. Clonar el repositorio (o descargar la carpeta)
cd ProyectoIntermodular

# 2. Levantar todos los servicios
docker compose up -d

# 3. Ver el estado
docker compose ps
```

El primer arranque puede tardar unos minutos mientras se instalan las dependencias de Composer y npm. Una vez listos, **ejecuta las migraciones y el seeder**:

```bash
docker compose exec backend php artisan migrate --seed
```

Esto crea todas las tablas y carga los datos de demo, incluyendo el usuario administrador:
- **Admin:** `admin@weddingplanner.com` / `admin1234`
- **Cliente con boda pendiente:** `lucia@example.com` / `cliente1234`
- **Cliente con boda activa:** `carlos@example.com` / `cliente1234`

Para parar todo:

```bash
docker compose down            # mantiene los datos de MySQL
docker compose down -v         # ELIMINA el volumen de MySQL (¡cuidado!)
```

---

## Estructura del repositorio

```
ProyectoIntermodular/
├── docker-compose.yml          # Orquestación de los 5 servicios
├── README.md                   # Este archivo
├── .gitignore
├── frontend/                   # Angular 21 (se inicializa en el paso 4)
│   ├── Dockerfile              # Multi-stage: development / production
│   ├── nginx.conf              # Configuración nginx para el target prod
│   └── .dockerignore
└── backend/                    # Laravel 11 (se inicializa en el paso 2)
    ├── Dockerfile              # PHP 8.3 + Composer + extensiones
    └── .dockerignore
```

Las carpetas `frontend/` y `backend/` contienen los proyectos Angular y Laravel completos.

---

## Comandos útiles

### Logs

```bash
docker compose logs -f frontend
docker compose logs -f backend
docker compose logs -f mysql
```

### Entrar a un contenedor

```bash
docker compose exec backend bash      # shell en el contenedor de Laravel
docker compose exec frontend sh       # shell en el contenedor de Angular
docker compose exec mysql bash        # shell en el contenedor de MySQL
```

### Ejecutar comandos de Laravel

```bash
docker compose exec backend php artisan migrate
docker compose exec backend php artisan db:seed
docker compose exec backend php artisan tinker
```

### Ejecutar comandos de Angular

```bash
docker compose exec frontend ng generate component nombre-componente
docker compose exec frontend ng build
```

### Reconstruir imágenes (tras tocar Dockerfiles)

```bash
docker compose build --no-cache
docker compose up -d
```

---

## Build de producción del frontend

El `Dockerfile` del frontend tiene tres targets: `development`, `build` y `production`.
Por defecto `docker-compose.yml` usa `development`. Para producción:

```bash
docker build -t wedding-planner-frontend:prod --target production ./frontend
docker run -d -p 80:80 wedding-planner-frontend:prod
```

---
