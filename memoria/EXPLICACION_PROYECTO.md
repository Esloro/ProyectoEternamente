# Wedding Planner — Explicación completa del proyecto

> Documento de defensa del Trabajo de Fin de Grado (2º DAW)
> Autora: Esmeralda López Rodero
> Proyecto: aplicación web para una agencia de Wedding Planner
> Stack: Angular 21 (frontend) + Laravel 11 (backend) + MySQL 8 + Docker

---

## Índice

1. Visión global del proyecto
2. Justificación del stack tecnológico
3. Arquitectura general
4. Infraestructura: Docker y Docker Compose
5. Base de datos: modelo entidad-relación
6. Backend Laravel paso a paso
7. Frontend Angular paso a paso
8. Flujos completos de negocio
9. Aspectos transversales (seguridad, validación, UX)
10. Cómo levantar y demostrar el proyecto
11. Posibles preguntas del tribunal y respuestas
12. Limitaciones conscientes y posibles mejoras

---

## 1. Visión global del proyecto

### 1.1. ¿Qué es?

Una **aplicación web completa para una agencia de wedding planner** llamada *Eternamente*. La aplicación cubre dos públicos muy distintos:

- **Público general (no logueado):** una landing page comercial donde la agencia muestra sus paquetes, su portfolio de bodas anteriores, los testimonios de clientes y un formulario de contacto.
- **Clientes registrados:** un panel privado donde, una vez la agencia ha activado su boda, el cliente puede personalizar todos los aspectos (lugar, floristería, catering, fotografía, etc.), gestionar sus invitados y mesas con drag & drop, ver el presupuesto estimado y chatear directamente con la wedding planner.
- **Administrador:** un panel de gestión interno donde la wedding planner controla clientes, bodas, catálogo de proveedores, mensajes recibidos, paquetes, portfolio, testimonios y conversaciones de chat.

### 1.2. ¿Por qué tiene sentido como negocio?

Una de las preguntas que más fácilmente puede hacerte el tribunal es **"¿por qué la agencia querría que el cliente vea precios y elija proveedores él mismo si una boda real se cierra con reuniones?"**.

La respuesta está en el flujo de estados que diseñé conscientemente:

1. El cliente se registra y rellena un **cuestionario inicial** con los datos básicos de su boda.
2. La boda queda en estado `pendiente_reunion` y el cliente solo ve un mensaje de **"estamos en contacto contigo para concertar la reunión inicial"**.
3. La agencia hace su reunión presencial (o telefónica) con el cliente, le presenta paquetes y precios reales, y **una vez contratado el servicio**, el administrador cambia el estado de la boda a `activa`.
4. **Solo entonces** el cliente desbloquea el panel completo de personalización.

Esto justifica que el cliente vea precios en el panel: ya ha pasado por la fase comercial, sabe lo que ha contratado, y la herramienta sirve para que él mismo agilice las elecciones internas (qué floristería de las que ya colaboran con la agencia prefiere, qué fotógrafo, qué música…) sin tener que reunirse para cada decisión individual. **Es una herramienta interna de la agencia, no un marketplace público**.

### 1.3. Alcance funcional

| Bloque | Funcionalidad |
| --- | --- |
| Landing pública | 7 secciones: hero, paquetes, portfolio, testimonios, sobre nosotros, contacto, footer. Animaciones scroll-reveal. |
| Autenticación | Registro, login, logout, verificación de email, recuperación de contraseña por enlace firmado, tokens Sanctum. |
| Cuestionario inicial | Formulario por pasos que crea la boda en estado pendiente. |
| Panel cliente | Resumen, personalización por categorías, organizador de mesas drag & drop, presupuesto, chat, perfil. |
| Panel admin | Dashboard con estadísticas, CRUD de clientes y bodas, CRUD de catálogo (proveedores, paquetes, portfolio, testimonios), mensajes de contacto, chat con todos los clientes. |
| Email | Verificación de email, recuperación de contraseña, notificación al admin cuando llega un mensaje de contacto. Mailhog en desarrollo. |
| Chat | Polling cada 7 segundos (sin WebSockets) con marcado de leídos. |

---

## 2. Justificación del stack tecnológico

Esta sección es muy importante para el tribunal: cada elección tiene una razón académica/técnica clara.

### 2.1. Frontend: Angular 21 con componentes standalone

**¿Por qué Angular y no React/Vue?**

- Angular es el framework que impartimos en el ciclo. Demuestra dominio de la herramienta enseñada.
- Aporta de fábrica todo lo necesario: routing, formularios reactivos, HTTP client, validación, animaciones, internacionalización, drag & drop. **Ningún competidor incluye tanto out-of-the-box.**
- El sistema de tipos de TypeScript me obliga a definir contratos claros entre servicios y componentes (`Boda`, `Usuario`, `RespuestaApi<T>`), lo que reduce muchísimo los bugs.

**¿Por qué Angular 21 y no una versión LTS más antigua?**

- Versión más reciente disponible al iniciar el proyecto. Permite usar **standalone components** (sin `NgModule`), **signals** y **control flow nuevo** (`@if`, `@for`).
- Desaparece el boilerplate de los módulos: cada componente declara sus dependencias en `imports: []`. El código es más cercano al de React funcional pero conservando las garantías de Angular.

**¿Qué son los signals y por qué los uso?**

Los signals son un sistema de estado reactivo introducido en Angular 16 y consolidado en Angular 21. Es básicamente una variable que **avisa automáticamente** a Angular cuando cambia, sin necesidad de `ChangeDetectionStrategy.OnPush` ni Observables.

Ejemplo en `autenticacion.service.ts:27`:

```ts
private _usuario = signal<Usuario | null>(this.cargarUsuarioPersistido());
readonly usuario = this._usuario.asReadonly();
readonly estaLogueado = computed(() => this._usuario() !== null);
readonly esAdministrador = computed(() => this._usuario()?.rol === 'administrador');
```

- `signal()` crea un valor reactivo.
- `computed()` crea un valor derivado que recalcula solo cuando cambian sus dependencias.
- `_usuario.asReadonly()` expone una versión de solo lectura para que ningún componente externo pueda modificar el estado por error.

**¿Por qué SCSS puro y no Tailwind/Bootstrap?**

- El enunciado del TFG pide **CSS hecho a mano** para demostrar que sé maquetar.
- SCSS me permite usar variables, anidación y mixins, manteniendo el control total del diseño y evitando la dependencia de frameworks que pueden quedar obsoletos.
- Se reutilizan variables globales (en `styles.scss`) para colores y tipografía, manteniendo coherencia visual.

### 2.2. Backend: Laravel 11

**¿Por qué Laravel y no Express/NestJS/Spring?**

- Laravel es el framework PHP más maduro y cubre todo lo que necesito sin instalar 15 paquetes:
  - Eloquent ORM con relaciones expresivas (`hasMany`, `belongsToMany`, `belongsTo`).
  - **Sanctum** para autenticación por tokens.
  - **Form Requests** para validación con mensajes en español.
  - **Notifications** para emails (verificación, reset password) sin tocar SMTP a mano.
  - **Migrations** y **Seeders** para versionar el esquema de BD.
- PHP es el lenguaje que también se da en el ciclo. Demuestra que domino el otro lado del stack web.
- **Convention over configuration**: si sigues las convenciones, en una mañana tienes login + verificación + reset password funcionando.

**Sanctum para autenticación**

Sanctum genera tokens opacos (no JWT) que se guardan en la tabla `personal_access_tokens`. Cada token está asociado a un usuario, se puede revocar individualmente (al hacer logout), y se valida con el middleware `auth:sanctum`. Es lo que recomienda Laravel para SPAs, más simple que JWT y suficientemente seguro.

### 2.3. Base de datos: MySQL 8

- Es la base de datos relacional que estudiamos.
- Mucho mejor soporte de Docker y phpMyAdmin que PostgreSQL para el alcance del proyecto.
- 100% de las relaciones del proyecto son tabulares y bien definidas → relacional encaja perfecto. NoSQL no aportaría nada.

### 2.4. Docker como capa de despliegue

- El tribunal puede levantar el proyecto en **un solo comando** (`docker compose up -d`) sin instalar PHP, Node, Composer, npm ni MySQL en su máquina.
- Garantiza que el proyecto se ejecuta exactamente igual en cualquier sistema operativo.
- Aprendí Docker como objetivo extra del módulo de Despliegue de Aplicaciones Web.

### 2.5. Mailhog para email de desarrollo

- Es un servidor SMTP falso. Captura todos los emails que envía Laravel y los muestra en una interfaz web (`http://localhost:8025`) para que pueda ver los emails de verificación y de recuperación de contraseña sin enviar correo real ni configurar Gmail.
- En producción se sustituiría por un servicio SMTP real (Mailgun, SendGrid, etc.) cambiando solo las variables de entorno.

### 2.6. Lo que descarté conscientemente

| Tecnología | Por qué la descarté |
| --- | --- |
| **NgRx / Redux** | Para una app con autenticación + estado de boda, signals son suficiente. NgRx triplicaría el código sin valor real. |
| **WebSockets / Pusher** | El chat funciona perfecto con polling cada 7 s. Añadir un broker de mensajería es complejidad innecesaria para un TFG. |
| **JWT** | Sanctum cubre lo mismo con tokens opacos y revocables, integrado de fábrica con Laravel. |
| **Tailwind / Bootstrap** | El enunciado pide CSS a mano para demostrar competencia maquetando. |
| **Server-Side Rendering (Angular Universal)** | La landing pública no necesita SEO (es una agencia local) y SSR complica el despliegue Docker. |
| **Microservicios** | Una API monolítica con secciones bien separadas es lo razonable para el tamaño del problema. |

---

## 3. Arquitectura general

### 3.1. Diagrama lógico

```
┌────────────────────────────────────────────────────────────────────┐
│                         NAVEGADOR                                  │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │  Angular SPA (puerto 4200 en dev, 80 en prod)                │  │
│  │  - Standalone components                                      │  │
│  │  - Lazy routes (admin, panel)                                 │  │
│  │  - Signals (estado reactivo)                                  │  │
│  │  - HTTP Interceptor (token Bearer + manejo 401)               │  │
│  │  - Guards: authGuard / adminGuard / bodaActivaGuard           │  │
│  └────────────────────────────────────────────────────────────────┐│
│                              │ HTTP/JSON                          ││
│                              ▼                                     ││
│  ┌──────────────────────────────────────────────────────────────┐ ││
│  │  Laravel API REST (puerto 8000)                               │ ││
│  │  /api/auth/...      AutenticacionController                   │ ││
│  │  /api/mi-boda/...   BodaController, MesaController, ...       │ ││
│  │  /api/admin/...     Admin\BodaController, Admin\Dashboard...  │ ││
│  │                                                               │ ││
│  │  Middlewares: auth:sanctum + verified + boda.activa + admin   │ ││
│  └──────────────────────────────────────────────────────────────┘ ││
│         │                                            │             ││
│         ▼                                            ▼             ││
│  ┌──────────────┐                          ┌──────────────────┐    ││
│  │ MySQL 8      │                          │ Mailhog (SMTP)   │    ││
│  │ port 3306    │                          │ port 1025/8025   │    ││
│  └──────────────┘                          └──────────────────┘    ││
└─────────────────────────────────────────────────────────────────────┘
```

### 3.2. Patrón general

- **Separación frontend/backend** clara. El frontend es 100% una SPA, el backend es 100% API REST. **Sin Blade, sin renderizado en servidor**. Esto facilita en el futuro reutilizar la API para una app móvil.
- **Estado en el cliente**: el token de Sanctum se guarda en `localStorage`, junto con el snapshot del usuario. Al recargar la página, el frontend recupera la sesión sin volver a pedir contraseña.
- **Autorización en cascada de middlewares**:
  - `auth:sanctum` → verifica que el token Bearer es válido.
  - `verified` → exige que el email esté verificado.
  - `boda.activa` → exige que la boda del cliente esté en estado `activa` (custom).
  - `es.administrador` → exige rol administrador (custom).

### 3.3. Estructura de carpetas

```
ProyectoIntermodular/
├── docker-compose.yml         # Orquesta los 5 servicios
├── README.md
├── memoria/                   # Memoria del TFG
├── frontend/
│   ├── Dockerfile             # Multi-stage: development + production
│   ├── nginx.conf             # Para el target production
│   └── src/
│       ├── environments/
│       └── app/
│           ├── app.config.ts
│           ├── app.routes.ts
│           ├── componentes/   # Componentes reutilizables (navbar, hero, footer, secciones)
│           ├── paginas/       # Páginas principales (landing, login, paneles)
│           ├── servicios/     # Lógica de comunicación con la API
│           ├── modelos/       # Interfaces TypeScript
│           ├── guardianes/    # Route guards
│           ├── interceptores/ # HTTP interceptors
│           └── directivas/    # Directivas custom (scroll-reveal)
└── backend/
    ├── Dockerfile             # PHP 8.3 + Composer + extensiones
    ├── app/
    │   ├── Models/            # Eloquent models (Usuario, Boda, ...)
    │   ├── Http/
    │   │   ├── Controllers/Api/   # Controladores REST públicos y de cliente
    │   │   ├── Controllers/Api/Admin/  # Controladores administrativos
    │   │   ├── Requests/      # Form Requests para validación
    │   │   └── Middleware/    # EsAdministrador, BodaDebeEstarActiva
    │   ├── Notifications/     # Emails personalizados
    │   ├── Mail/              # Mailables
    │   └── Providers/
    ├── database/
    │   ├── migrations/        # Versionado del esquema
    │   └── seeders/           # Datos iniciales (admin, clientes demo, proveedores)
    └── routes/
        ├── api.php            # Todas las rutas REST
        └── web.php
```

---

## 4. Infraestructura: Docker y Docker Compose

### 4.1. `docker-compose.yml`

Orquesta **5 servicios**:

1. **`frontend`** — Angular 21 con `ng serve` y hot reload (puerto 4200).
2. **`backend`** — Laravel 11 con `php artisan serve` (puerto 8000).
3. **`mysql`** — MySQL 8 con volumen persistente `mysql_datos` (puerto 3306).
4. **`phpmyadmin`** — interfaz web para inspeccionar la BD (puerto 8080).
5. **`mailhog`** — bandeja de correo de desarrollo (puerto 8025 web, 1025 SMTP).

**Detalles importantes que demuestran que entiendo Docker:**

- **Volumen anónimo `/app/node_modules` y `/var/www/html/vendor`**: el bind-mount de Windows trae la carpeta vacía del host, lo que **borraría** los `node_modules` y el `vendor` de Composer instalados en la imagen. La solución es declarar un volumen anónimo sobre esa subruta para que tenga prioridad sobre el bind-mount.
- **`CHOKIDAR_USEPOLLING=true`** y **`--poll 2000`**: en Windows, el sistema de archivos no notifica cambios al kernel del contenedor, así que activamos polling para que `ng serve` detecte los cambios.
- **`depends_on` con `condition: service_healthy`**: el backend espera a que MySQL esté **realmente listo**, no solo arrancado, gracias al `healthcheck` que hace `mysqladmin ping`. Así evito el típico error de "MySQL no responde" en el primer arranque.
- **Red interna `wp_red`**: los servicios se comunican entre sí por nombre (`mysql`, `mailhog`), no necesitan exponer puertos al host (aunque los exponemos para poder inspeccionarlos).

### 4.2. `backend/Dockerfile`

```dockerfile
FROM php:8.3-cli
```

- **Extensiones de PHP** instaladas para Laravel: `pdo_mysql`, `mbstring`, `gd`, `zip`, `opcache`...
- **OPcache configurado**: sin OPcache, en Windows + Docker, cada request tarda 8-10 s porque PHP recompila los miles de archivos del framework. Con OPcache, las peticiones bajan a 100-300 ms.
- **`composer install` durante el build**: pre-instalo las dependencias en la imagen, así el contenedor arranca con `vendor/` ya listo (sin esperar minutos en cada `up`).
- **Entrypoint inteligente**: si no existe `.env`, copia `.env.example` y genera la APP_KEY automáticamente.

### 4.3. `frontend/Dockerfile`

Multi-stage build con tres targets:

- **`development`**: Node 22 + Angular CLI 21 global, ejecuta `npm start -- --host 0.0.0.0 --port 4200 --poll 2000`.
- **`build`**: ejecuta `npm run build -- --configuration=production` y produce los estáticos compilados en `/app/dist/*/browser/`.
- **`production`**: imagen `nginx:alpine` que sirve los estáticos compilados.

Esto demuestra que **entiendo la diferencia entre desarrollo y producción** y que tengo la app preparada para desplegar. Para ejecutar el target de producción:

```bash
docker build -t wedding-planner-frontend:prod --target production ./frontend
docker run -d -p 80:80 wedding-planner-frontend:prod
```

---

## 5. Base de datos: modelo entidad-relación

### 5.1. Tablas principales

| Tabla | Propósito | Relaciones |
| --- | --- | --- |
| `usuarios` | Cuentas de cliente y administrador | 1:N con `bodas`, `mensajes` |
| `bodas` | Una por cliente, estado del flujo | N:1 a `usuarios`, 1:N a `mesas`, `invitados`, `mensajes`, M:N a `proveedores` |
| `proveedores` | Catálogo (lugar, floristería, DJ, fotografía, …) | M:N con `bodas` mediante `boda_proveedor` |
| `boda_proveedor` | Pivote con `notas` | — |
| `mesas` | Mesas del banquete | N:1 a `bodas`, 1:N a `invitados` |
| `invitados` | Lista de invitados | N:1 a `bodas`, N:1 opcional a `mesas` |
| `mensajes` | Chat cliente↔admin | N:1 a `bodas`, `usuarios` (emisor/receptor) |
| `paquetes` | Los 3 paquetes públicos de la landing | — |
| `portfolio` | Galería pública de bodas pasadas | — |
| `testimonios` | Reseñas de clientes (con `verificado`) | — |
| `mensajes_contacto` | Formulario público de contacto | — |
| `password_reset_tokens` | Estándar de Laravel para reset password | — |
| `sessions` | Estándar de Laravel | — |
| `personal_access_tokens` | Tokens Sanctum | — |

### 5.2. Decisiones del modelo

#### Por qué `usuarios` y no `users`

Todo el modelo está **en español** (es un requisito del TFG y del enunciado). Esto incluye nombres de tabla, columnas, modelos PHP, controladores, servicios Angular, rutas y variables. La única excepción son las tablas estándar de Laravel (`sessions`, `password_reset_tokens`, `personal_access_tokens`) porque Laravel construye consultas internas que las buscan por su nombre original.

Para que Laravel use mi modelo `Usuario` en lugar del `User` por defecto, en `AppServiceProvider::boot()` registro:

```php
$this->app['config']->set('auth.providers.users.model', Usuario::class);
```

#### Por qué `email_verificado_en` y no `email_verified_at`

Tradición de Laravel: la columna se llama `email_verified_at`. En el modelo `Usuario` sobreescribo los métodos de la interfaz `MustVerifyEmail` para que apunten a la columna en español:

```php
public function hasVerifiedEmail(): bool {
    return ! is_null($this->email_verificado_en);
}
public function markEmailAsVerified(): bool {
    return $this->forceFill(['email_verificado_en' => $this->freshTimestamp()])->save();
}
```

#### Por qué los ENUMs sin tildes

Las columnas `franja_horaria`, `tipo_ceremonia`, `tematica`, etc. son ENUMs en MySQL. Las almacené sin tildes (`manana` en vez de `mañana`) para evitar problemas de codificación entre PHP, MySQL y JSON. En el frontend, un mapa de etiquetas (`ETIQUETAS_FRANJA`, `ETIQUETAS_CEREMONIA`...) traduce el valor técnico a su etiqueta humana ("Mañana"). Ver `frontend/src/app/modelos/boda.model.ts:54-105`.

#### Por qué las claves foráneas con `cascadeOnDelete`

Si elimino una boda, todas sus mesas, invitados y mensajes deben desaparecer también. La integridad referencial garantizada por la BD evita que queden registros "huérfanos".

#### Por qué `presupuesto_estimado` y `presupuesto_definitivo` son `DECIMAL(10,2)`

Para precios y dinero, **nunca uses FLOAT**: introduce errores de redondeo (un `0.1 + 0.2` no es `0.3`). DECIMAL(10,2) garantiza precisión de céntimo hasta 99.999.999,99 €.

#### Por qué `num_acompanantes` y no una tabla de "acompañantes"

Para el alcance del TFG, modelar acompañantes como una entidad separada complicaría todo (CRUD para una tabla más, drag & drop más complejo, capacidad de mesas más opaca). Modelar como entero **funciona**: cada `Invitado` tiene un nombre, alergias, y un número de acompañantes. Las plazas que ocupa en una mesa son `1 + num_acompanantes`. Ver `Invitado::plazasQueOcupa()` y `Mesa::plazasOcupadas()`.

### 5.3. Seeders

`DatabaseSeeder` ejecuta los siguientes en orden (importante: las dependencias):

1. `UsuarioSeeder` — admin + 2 clientes demo.
2. `ProveedorSeeder` — ~15 proveedores variados.
3. `PaqueteSeeder` — 3 paquetes públicos.
4. `PortfolioSeeder` — 6-8 bodas en portfolio.
5. `TestimonioSeeder` — 5 testimonios.
6. `BodaSeeder` — bodas de los clientes demo (uno pendiente, otro activo) con sus mesas, invitados, proveedores y mensajes.

Esto da un **entorno de demo realista** desde el primer arranque, lo cual es clave para que el tribunal pueda probar la app sin tener que rellenar 30 formularios primero.

### 5.4. Migraciones nombradas con fecha

Las migraciones están numeradas (`2026_04_25_100001_create_usuarios_table.php`, `..._100002_...`, etc.) para garantizar que se ejecuten en el orden correcto. Esto es importante porque la tabla `bodas` referencia `usuarios`, y `boda_proveedor` referencia ambas.

---

## 6. Backend Laravel paso a paso

### 6.1. Estructura

```
backend/app/
├── Models/                 # Eloquent: Usuario, Boda, Proveedor, ...
├── Http/
│   ├── Controllers/
│   │   ├── Controller.php  # Base con helpers ok() y ko()
│   │   └── Api/
│   │       ├── AutenticacionController.php
│   │       ├── BodaController.php
│   │       ├── MensajeController.php
│   │       ├── ...
│   │       └── Admin/
│   │           ├── BodaController.php
│   │           ├── DashboardController.php
│   │           └── ...
│   ├── Requests/           # Form Requests con reglas + mensajes
│   └── Middleware/
│       ├── EsAdministrador.php
│       └── BodaDebeEstarActiva.php
├── Notifications/          # Emails personalizados
├── Mail/                   # Mailables
└── Providers/AppServiceProvider.php
```

### 6.2. Controlador base con respuesta JSON uniforme

`app/Http/Controllers/Controller.php`:

```php
abstract class Controller {
    protected function ok(mixed $data = null, string $mensaje = '', int $codigo = 200) {
        return response()->json([
            'success' => true, 'data' => $data, 'message' => $mensaje,
        ], $codigo);
    }
    protected function ko(string $mensaje, int $codigo = 400, mixed $errores = null) {
        return response()->json([
            'success' => false, 'data' => $errores, 'message' => $mensaje,
        ], $codigo);
    }
}
```

**¿Por qué esto?** Porque establece un **contrato uniforme** entre backend y frontend: **toda** respuesta de la API tiene la misma forma `{ success, data, message }`. El frontend tiene un único interface `RespuestaApi<T>` que cubre cualquier endpoint. Si en algún momento el código devolviera otra forma, TypeScript saltaría inmediatamente.

### 6.3. Modelos Eloquent

Los modelos siguen las **convenciones de Laravel** pero con tablas en español:

```php
class Usuario extends Authenticatable implements MustVerifyEmail {
    protected $table = 'usuarios';
    protected $fillable = ['nombre', 'apellidos', 'email', 'telefono', 'password', 'rol'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'email_verificado_en' => 'datetime',
        'password' => 'hashed',  // Hashea automáticamente al asignar
    ];
}
```

**Casts importantes:**

- `'password' => 'hashed'`: cuando hago `$usuario->password = '1234'`, Laravel lo hashea con bcrypt automáticamente.
- `'fecha_boda' => 'date:Y-m-d'`: serializa la fecha como `2026-08-15`, sin hora ni zona horaria. Esto evita un bug que tuve al principio: con `APP_TIMEZONE=Europe/Madrid`, Carbon convertía la fecha a UTC y le restaba 2 horas, lo que en el frontend (que hacía `substring(0, 10)`) se traducía en perder un día en cada save.

**Relaciones expresivas:**

```php
// Usuario.php
public function bodas(): HasMany {
    return $this->hasMany(Boda::class, 'usuario_id');
}

// Boda.php
public function proveedores(): BelongsToMany {
    return $this->belongsToMany(Proveedor::class, 'boda_proveedor')
        ->withPivot('notas')
        ->withTimestamps();
}
```

Con esto, hago `$usuario->bodas` y obtengo una colección de bodas. `$boda->proveedores` da los proveedores elegidos, e incluye el campo `notas` del pivote.

**Constantes para estados:**

```php
public const ESTADO_PENDIENTE = 'pendiente_reunion';
public const ESTADO_ACTIVA    = 'activa';
public const ESTADO_FINALIZADA = 'finalizada';
public const ESTADO_CANCELADA  = 'cancelada';
```

Así nunca tengo strings mágicos repartidos por el código. Si algún día cambia el nombre de un estado, lo modifico en un solo sitio.

### 6.4. Sanctum para autenticación

Sanctum es el sistema oficial de Laravel para autenticación de SPAs. Funciona así:

1. El cliente hace `POST /api/auth/login` con email + contraseña.
2. El backend verifica con `Hash::check(...)`.
3. Si es correcto, llama a `$usuario->createToken('auth-token')->plainTextToken`. Esto:
   - Genera un token aleatorio (`base64`).
   - Lo guarda hasheado en la tabla `personal_access_tokens`.
   - Devuelve el texto en claro **solo esta vez**.
4. El frontend lo guarda en `localStorage`.
5. En cada petición siguiente, el frontend añade `Authorization: Bearer <token>` (vía interceptor).
6. El middleware `auth:sanctum` busca el token en la BD, comprueba el hash, y carga el usuario.
7. Al hacer logout, llamo a `currentAccessToken()->delete()` para invalidarlo.

```php
// AutenticacionController::login
$usuario = Usuario::where('email', $request->email)->first();
if (! $usuario || ! Hash::check($request->password, $usuario->password)) {
    return $this->ko('Credenciales incorrectas.', 401);
}
$token = $usuario->createToken('auth-token')->plainTextToken;
return $this->ok(['usuario' => $usuario, 'token' => $token], 'Login correcto.');
```

### 6.5. Verificación de email

Laravel trae todo el flujo de verificación de email integrado, pero tuve que adaptarlo:

1. **En el registro**, después de crear el usuario, disparo el evento `Registered`:
   ```php
   event(new Registered($usuario));
   ```
   Laravel automáticamente envía un email con un enlace firmado.
2. **El email está en español**, gracias a `VerificarEmailNotification` que sustituye al notification por defecto. La sustitución se registra en `AppServiceProvider::boot()`:
   ```php
   VerifyEmail::toMailUsing(function ($notifiable, string $url) {
       return (new VerificarEmailNotification)->toMail($notifiable);
   });
   ```
3. **El enlace del email apunta a la API**, no al frontend, porque el frontend no puede comprobar la firma criptográfica. La ruta tiene el nombre `verification.verify`, que es lo que Laravel busca por convención. La firma se valida con el middleware `signed`.
4. **Tras verificar**, la API redirige al frontend a `/email-verificado?ok=1` y el frontend muestra una pantalla bonita de "tu email está confirmado".

### 6.6. Middlewares custom

#### `EsAdministrador.php`

Bloquea las rutas administrativas si el usuario no es admin:

```php
if (! $usuario || ! $usuario->esAdministrador()) {
    return response()->json([
        'success' => false, 'data' => null,
        'message' => 'No tienes permiso para acceder a esta seccion.',
    ], 403);
}
return $siguiente($request);
```

Se aplica en `routes/api.php`:
```php
Route::prefix('admin')->middleware(['auth:sanctum', 'verified', 'es.administrador'])->group(...)
```

#### `BodaDebeEstarActiva.php`

Esta es la pieza **más importante de la lógica de negocio** del proyecto. Bloquea acceso a las funcionalidades del panel completo (proveedores, mesas, chat) hasta que el admin haya activado la boda:

```php
$boda = $usuario?->bodas()->latest()->first();

if (! $boda) {
    return response()->json([...], 403); // No ha rellenado el cuestionario
}
if (! $boda->estaActiva()) {
    return response()->json([...], 403); // Boda en pendiente_reunion
}

// Inyectamos la boda en el request para que los controladores la usen
$request->attributes->set('boda', $boda);
return $siguiente($request);
```

El truco fino: **inyecto la boda en `$request->attributes`** para que los controladores que vienen después no tengan que volver a consultar la BD. Es un pequeño patrón de optimización que demuestra entender el ciclo de vida del request.

### 6.7. Form Requests para validación

Cada endpoint que recibe datos tiene su propio `FormRequest`. Ejemplo `RegistroRequest.php`:

```php
public function rules(): array {
    return [
        'nombre'    => ['required', 'string', 'max:100'],
        'apellidos' => ['required', 'string', 'max:150'],
        'email'     => ['required', 'string', 'email', 'max:150', 'unique:usuarios,email'],
        'telefono'  => ['nullable', 'string', 'max:20'],
        'password'  => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
    ];
}

public function messages(): array {
    return [
        'email.unique'       => 'Ya existe una cuenta con este email.',
        'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
        'password.mixed'     => 'La contraseña debe combinar mayusculas y minusculas.',
        // ...
    ];
}
```

**Ventajas:**

- La validación está **fuera del controlador**, que queda limpio.
- Los **mensajes están en español** y ya formateados para mostrarse al usuario.
- Si la validación falla, Laravel devuelve automáticamente un 422 con `{ message, errors }`. El frontend lee esos errores y los pinta en el formulario.

### 6.8. Estructura de rutas (`routes/api.php`)

Las rutas están **agrupadas por nivel de privilegio**, lo cual hace que la API sea fácil de entender de un vistazo:

```php
// 1. Públicas (landing)
Route::get('/paquetes',     [PaqueteController::class, 'index']);
Route::get('/portfolio',    [PortfolioController::class, 'index']);
Route::get('/testimonios',  [TestimonioController::class, 'index']);
Route::post('/contacto',    [MensajeContactoController::class, 'store']);

// 2. Autenticación
Route::prefix('auth')->group(function () {
    Route::post('/registro',   ...);
    Route::post('/login',      ...);
    // ...
});

// 3. Cliente con email verificado
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/mi-boda', ...);
    Route::post('/mi-boda/cuestionario', ...);

    // 4. Cliente con boda activa (subgrupo)
    Route::middleware('boda.activa')->group(function () {
        Route::get('/proveedores', ...);
        Route::apiResource('mi-boda/invitados', InvitadoController::class);
        // ...
    });
});

// 5. Administrador
Route::prefix('admin')->middleware(['auth:sanctum', 'verified', 'es.administrador'])->group(function () {
    Route::get('/dashboard', ...);
    Route::get('/clientes', ...);
    // ...
});
```

**Observa la composición de middlewares**: `boda.activa` se aplica DENTRO del grupo de `auth:sanctum + verified`. Cada nivel **acumula** los anteriores. Esto demuestra que entiendo el sistema de middlewares y autorización de Laravel.

### 6.9. Cambio de estado de la boda (lógica clave)

`Admin\BodaController::cambiarEstado` es donde el admin "activa" la boda:

```php
public function cambiarEstado(Request $request, Boda $boda) {
    $datos = $request->validate([
        'estado' => ['required', Rule::in([
            Boda::ESTADO_PENDIENTE, Boda::ESTADO_ACTIVA,
            Boda::ESTADO_FINALIZADA, Boda::ESTADO_CANCELADA,
        ])],
    ]);
    $boda->update(['estado' => $datos['estado']]);
    return $this->ok(['boda' => $boda], 'Estado actualizado a "' . $datos['estado'] . '".');
}
```

Cuando el admin pulsa "activar" en el panel, el frontend hace `PUT /api/admin/bodas/{boda}/estado` con `{ estado: 'activa' }`, y a partir de ese momento el cliente puede entrar a las secciones del panel completo.

### 6.10. Recálculo de presupuesto al guardar proveedores

```php
public function guardarProveedores(GuardarProveedoresRequest $request) {
    $boda = $request->attributes->get('boda');
    $sincronizar = [];
    foreach ($request->proveedores as $proveedor) {
        $sincronizar[$proveedor['id']] = ['notas' => $proveedor['notas'] ?? null];
    }
    $boda->proveedores()->sync($sincronizar);
    $boda->recalcularPresupuesto();
    return $this->ok(['boda' => $boda->fresh('proveedores')], '...');
}
```

`sync()` es un método de Eloquent que:
- Inserta las relaciones que no existen.
- Actualiza las que sí existen (con `notas` como pivot data).
- **Borra** las que ya no están en el array.

Es perfecto para "guardar la selección actual": el cliente puede marcar y desmarcar proveedores y al guardar, todo se sincroniza.

`recalcularPresupuesto()` (en el modelo `Boda`) suma los precios de los proveedores elegidos:
```php
public function recalcularPresupuesto(): void {
    $total = $this->proveedores()->sum('precio');
    $this->update(['presupuesto_estimado' => $total]);
}
```

---

## 7. Frontend Angular paso a paso

### 7.1. Bootstrap y configuración global

`main.ts` arranca la aplicación; toda la configuración está en `app/app.config.ts`:

```ts
export const appConfig: ApplicationConfig = {
  providers: [
    provideBrowserGlobalErrorListeners(),
    provideRouter(routes, withComponentInputBinding()),
    provideHttpClient(withFetch(), withInterceptors([authTokenInterceptor])),
    provideAnimations(),
    { provide: LOCALE_ID, useValue: 'es-ES' },
  ],
};
```

- **`provideRouter(routes, withComponentInputBinding())`**: el router con binding automático de inputs (cuando una ruta tiene `:id`, Angular lo pasa como input al componente).
- **`provideHttpClient(withFetch(), withInterceptors([...]))`**: HTTP client moderno usando `fetch()` en lugar de `XMLHttpRequest`, con interceptor.
- **`provideAnimations()`**: necesario para que Angular Animations funcione. **Si lo olvidas, la página queda en blanco** (lo aprendí por las malas).
- **`LOCALE_ID: 'es-ES'`**: pipes como `DecimalPipe` y `DatePipe` formatean en español.

### 7.2. Standalone components

Cada componente se declara en su propio archivo, sin `NgModule`:

```ts
@Component({
  selector: 'app-landing',
  standalone: true,
  imports: [Navbar, Hero, SeccionPaquetes, SeccionPortfolio, ...],
  templateUrl: './landing.html',
  styleUrl: './landing.scss',
})
export class Landing {}
```

**Ventajas:**

- No hay módulos. Cada componente declara explícitamente sus dependencias.
- El bundle final es más pequeño porque el tree-shaking funciona mejor.
- Nuevos en Angular 21: solo me preocupo del componente que estoy escribiendo.

### 7.3. Lazy loading de rutas (`app.routes.ts`)

```ts
export const routes: Routes = [
  { path: '', loadComponent: () => import('./paginas/landing/landing').then((m) => m.Landing) },
  { path: 'login', loadComponent: () => import('./paginas/login/login').then((m) => m.Login) },
  // ...
  {
    path: 'panel',
    canActivate: [authGuard],
    loadChildren: () => import('./paginas/panel-cliente/panel-cliente.routes').then((m) => m.PANEL_CLIENTE_ROUTES),
  },
  {
    path: 'admin',
    canActivate: [authGuard, adminGuard],
    loadChildren: () => import('./paginas/panel-admin/panel-admin.routes').then((m) => m.PANEL_ADMIN_ROUTES),
  },
  { path: '**', redirectTo: '' },
];
```

**Ventajas del lazy loading:**

- El navegador **solo descarga el código del panel admin si el usuario entra al panel admin**. Un visitante anónimo de la landing nunca descarga el código del panel.
- Reduce el tiempo de carga inicial.
- Cada panel tiene su propio fichero de rutas (`panel-cliente.routes.ts`, `panel-admin.routes.ts`).

### 7.4. Guards (canActivate)

Tres guards funcionales:

#### `authGuard` (frontend/src/app/guardianes/auth.guard.ts)

```ts
export const authGuard: CanActivateFn = (_ruta, estado) => {
  const auth = inject(AutenticacionService);
  const router = inject(Router);
  if (auth.estaLogueado()) return true;
  return router.createUrlTree(['/login'], { queryParams: { returnUrl: estado.url } });
};
```

Si no estás logueado, redirige a `/login` **conservando la URL solicitada como `returnUrl`** para volver tras el login.

#### `adminGuard`

```ts
export const adminGuard: CanActivateFn = () => {
  const auth = inject(AutenticacionService);
  const router = inject(Router);
  if (!auth.estaLogueado()) return router.createUrlTree(['/login']);
  if (!auth.esAdministrador()) return router.createUrlTree(['/panel']);
  return true;
};
```

#### `bodaActivaGuard`

Este guard es **asíncrono**: pregunta al backend si la boda está activa antes de dejar entrar a `/panel/personalizacion`, `/panel/mesas`, `/panel/chat`, etc.

```ts
export const bodaActivaGuard: CanActivateFn = () => {
  const boda = inject(BodaService);
  const router = inject(Router);
  return boda.miBoda().pipe(
    map((respuesta) => {
      const datos = respuesta.data.boda;
      if (!datos) return router.createUrlTree(['/panel/cuestionario']);
      if (datos.estado !== 'activa') return router.createUrlTree(['/panel/pendiente']);
      return true;
    }),
    catchError(() => of(router.createUrlTree(['/login']))),
  );
};
```

Esto es **defensa en profundidad**: aunque el backend ya bloquea con `boda.activa`, el frontend bloquea ANTES, y muestra una experiencia de usuario mejor (redirige a la pantalla apropiada en vez de mostrar "403").

### 7.5. HTTP Interceptor

`auth-token.interceptor.ts`:

```ts
export const authTokenInterceptor: HttpInterceptorFn = (peticion, siguiente) => {
  const auth = inject(AutenticacionService);
  let peticionFinal = peticion;
  const token = auth.obtenerToken();

  if (token && peticion.url.startsWith(environment.apiUrl)) {
    peticionFinal = peticion.clone({
      setHeaders: { Authorization: `Bearer ${token}` },
    });
  }

  return siguiente(peticionFinal).pipe(
    catchError((error: HttpErrorResponse) => {
      if (error.status === 401 && token) {
        auth.forzarCierreLocal();
      }
      return throwError(() => error);
    }),
  );
};
```

Hace dos cosas críticas:

1. **Añade el token Bearer** automáticamente a cada petición que vaya a nuestra API. Ningún componente tiene que preocuparse de eso.
2. **Si recibe un 401, fuerza el cierre de sesión local y redirige a `/login`**. Esto cubre el caso "el token caducó mientras el usuario navegaba". El frontend reacciona y limpia.
   - Importante: **solo cierra sesión si HABÍA token**. Si no, un login fallido (que también devuelve 401) no rompería el flujo.

### 7.6. Servicios y signals

`AutenticacionService` mantiene el estado del usuario logueado en un signal. Los componentes que dependen de "¿estoy logueado?" simplemente leen el signal y Angular se encarga de re-renderizar:

```ts
@Injectable({ providedIn: 'root' })
export class AutenticacionService {
  private _usuario = signal<Usuario | null>(this.cargarUsuarioPersistido());
  readonly usuario = this._usuario.asReadonly();
  readonly estaLogueado = computed(() => this._usuario() !== null);
  readonly esAdministrador = computed(() => this._usuario()?.rol === 'administrador');
  // ...
}
```

`BodaService` hace lo mismo con la boda actual:

```ts
readonly bodaActual = signal<Boda | null>(null);
miBoda(): Observable<...> {
  return this.http.get<...>(this.base).pipe(tap((r) => this.bodaActual.set(r.data.boda)));
}
```

Cualquier componente que muestre datos de la boda lee `bodaService.bodaActual()` y reacciona automáticamente cuando cambia. Sin Subjects ni RxJS extra.

### 7.7. Modelos TypeScript

En `frontend/src/app/modelos/` defino los **mismos contratos que el backend**, pero como interfaces TypeScript:

```ts
export interface Boda {
  id: number;
  usuario_id: number;
  tipo_ceremonia: TipoCeremonia;
  fecha_boda: string;
  num_invitados: number;
  estado: EstadoBoda;
  // ...
  proveedores?: Proveedor[];
  mesas?: Mesa[];
  invitados?: Invitado[];
}
```

Y el contrato de respuesta común:

```ts
export interface RespuestaApi<T = unknown> {
  success: boolean;
  data: T;
  message: string;
}
```

Así, cuando el componente recibe la respuesta, **TypeScript me autocompleta** todos los campos. Si el backend cambia un nombre de campo, el frontend rompe en compilación, no en producción.

### 7.8. Directiva custom: `appScrollReveal`

```ts
@Directive({ selector: '[appScrollReveal]', standalone: true })
export class ScrollRevealDirective implements OnInit, OnDestroy {
  // ...
  ngOnInit(): void {
    this.el.nativeElement.classList.add('scroll-reveal');
    this.observer = new IntersectionObserver((entradas) => {
      entradas.forEach((entrada) => {
        if (entrada.isIntersecting) {
          entrada.target.classList.add('revealed');
          this.observer?.unobserve(entrada.target);
        }
      });
    }, { threshold: 0.15 });
    this.observer.observe(this.el.nativeElement);
  }
}
```

**¿Qué hace?** Aplica una clase CSS `revealed` a una sección cuando entra en el viewport. Combinado con CSS:

```scss
.scroll-reveal { opacity: 0; transform: translateY(40px); transition: 0.8s ease; }
.scroll-reveal.revealed { opacity: 1; transform: none; }
```

Las secciones aparecen suavemente al hacer scroll. Esto demuestra:

- Conocimiento de **directivas custom** en Angular.
- Conocimiento de la **API IntersectionObserver** del navegador (que es la forma moderna y eficiente de detectar visibilidad, en lugar de escuchar `scroll` con throttle).
- Buen **gusto en UX**: la animación no es molesta porque solo se dispara una vez (`unobserve`).

### 7.9. Drag & drop con Angular CDK

El organizador de mesas usa `@angular/cdk/drag-drop`:

```ts
imports: [DragDropModule, ReactiveFormsModule],

// En el template (HTML):
// <div cdkDropList [cdkDropListData]="m.invitadosLista"
//      [cdkDropListConnectedTo]="dropListIds"
//      (cdkDropListDropped)="drop($event, m.id)">
//   <div *ngFor="let i of m.invitadosLista" cdkDrag>{{ i.nombre }}</div>
// </div>

protected drop(event: CdkDragDrop<Invitado[]>, mesaId: number | null): void {
  if (event.previousContainer === event.container) {
    moveItemInArray(event.container.data, event.previousIndex, event.currentIndex);
  } else {
    transferArrayItem(event.previousContainer.data, event.container.data,
      event.previousIndex, event.currentIndex);
    this.invitadoService.asignarMesa(invitado.id, mesaId).subscribe({
      error: () => this.cargarDatos(),  // Si el backend rechaza, recargo
    });
  }
}
```

Es la solución oficial de Angular para drag & drop. **Optimistic update**: actualizo el estado local inmediatamente y, si el backend falla (por ejemplo porque la mesa está llena), recargo todo desde el servidor.

### 7.10. Polling del chat

`ChatService::conversacionStream`:

```ts
conversacionStream(): Observable<RespuestaApi<RespuestaConversacion>> {
  return interval(environment.intervaloPollingChat).pipe(
    switchMap(() => this.conversacion()),
  );
}
```

`interval(7000)` emite cada 7 segundos. `switchMap` cancela la petición anterior si todavía está en curso cuando llega la nueva. El componente se suscribe en `ngOnInit` y desuscribe en `ngOnDestroy` para evitar memory leaks:

```ts
ngOnInit(): void {
  this.pollingSub = this.chatService.conversacionStream().subscribe({...});
}
ngOnDestroy(): void {
  this.pollingSub?.unsubscribe();
}
```

Esto es **suficiente** para un chat con tráfico normal. Si la app creciera mucho, sustituiría por WebSockets (Pusher, Laravel Reverb), pero para un TFG es lo correcto.

---

## 8. Flujos completos de negocio

### 8.1. Flujo "Nuevo cliente desde cero hasta panel completo"

1. **Visita la landing** en `http://localhost:4200`. Ve los paquetes, portfolio, testimonios. Decide registrarse pulsando "Contratar" en uno de los paquetes.
2. **Pantalla de registro**: rellena nombre, apellidos, email, teléfono, contraseña. El frontend valida con Reactive Forms (longitud mínima, formato email, password con mayúscula+minúscula+número). Al enviar, `POST /api/auth/registro`.
3. **Backend**:
   - `RegistroRequest` valida con las mismas reglas (defensa en profundidad).
   - Crea el usuario con `rol = 'cliente'`.
   - Dispara `Registered` → Laravel envía email de verificación.
   - Devuelve token Sanctum.
4. **Frontend**: guarda token y datos en `localStorage`. Redirige a `/panel/cuestionario`.
5. **Pantalla "verifica tu email"**: el cuestionario está deshabilitado hasta que el cliente confirme el email. Botón "reenviar email" para casos de extravío.
6. **El cliente abre Mailhog** (`http://localhost:8025`), ve el email "Confirma tu correo electrónico - Wedding Planner", pulsa el botón.
7. **Backend** valida la firma criptográfica del enlace. Marca `email_verificado_en = now()`. Redirige a `/email-verificado?ok=1`.
8. **Frontend** muestra una pantalla bonita "tu email ha sido verificado". El cliente vuelve al cuestionario.
9. **Cuestionario inicial** por pasos (4 pantallas):
   - Paso 1: nombre de la pareja, tipo de ceremonia, lugar de celebración.
   - Paso 2: fecha, número de invitados, franja horaria.
   - Paso 3: temática, tipo de comida.
   - Paso 4: presupuesto orientativo + revisión.
   - Al enviar, `POST /api/mi-boda/cuestionario` crea la boda en estado `pendiente_reunion`.
10. **Pantalla "pendiente de reunión"**: el cliente ve un mensaje cálido explicando que la wedding planner se pondrá en contacto. La opción de personalización está bloqueada (los items del menú lateral están en gris).
11. **Wedding planner hace su trabajo offline**: contacta al cliente, hacen reunión, firman contrato.
12. **Admin entra al panel admin** (`http://localhost:4200/admin`), va a Bodas, abre la del cliente, pulsa "Activar". `PUT /api/admin/bodas/{id}/estado` con `{ estado: 'activa' }`.
13. **El cliente recarga su panel**: ahora todas las opciones están desbloqueadas.
14. **Cliente personaliza su boda**:
    - Va a "Personalización": ve categorías (lugar, floristería, música, fotografía, catering, decoración, …). En cada categoría selecciona UNO de los proveedores.
    - El presupuesto estimado se actualiza al guardar.
15. **Cliente gestiona invitados y mesas**:
    - Crea mesas con número y capacidad.
    - Crea invitados (nombre, alergias, número de acompañantes).
    - Arrastra cada invitado a una mesa (drag & drop). Si la mesa está llena, el backend rechaza y el frontend recarga.
16. **Cliente pulsa "Solicitar presupuesto definitivo"**: se envía un mensaje al chat con el admin.
17. **Admin lee el mensaje en el chat**, abre la boda, fija el presupuesto definitivo (`PUT /api/admin/bodas/{id}/presupuesto`).
18. **Día de la boda**: admin cambia el estado a `finalizada`. La boda queda en histórico.

### 8.2. Flujo "Recuperación de contraseña"

1. Cliente pulsa "He olvidado mi contraseña" en `/login`.
2. Pantalla `/recuperar-password`: introduce su email.
3. `POST /api/auth/recuperar-password`. El backend:
   - Llama a `Password::sendResetLink($request->only('email'))`.
   - **Responde siempre OK**, aunque el email no exista (para no filtrar qué emails están registrados — es una buena práctica de seguridad).
4. Mailhog muestra el email con el enlace de reset.
5. El enlace lleva a `/recuperar-password?token=...&email=...` en el frontend.
6. Cliente introduce nueva contraseña + confirmación. `POST /api/auth/reset-password`.
7. Backend valida el token, hashea la nueva contraseña, e **invalida todos los tokens existentes** del usuario (por si el atacante había robado un token):
   ```php
   $usuario->forceFill(['password' => $password])->save();
   $usuario->tokens()->delete();
   ```

### 8.3. Flujo "Mensaje de contacto desde la landing"

1. Visitante anónimo rellena el formulario de contacto.
2. `POST /api/contacto` (sin auth). Validación con `MensajeContactoRequest`.
3. Backend guarda en `mensajes_contacto` y envía email al admin (mailable `MensajeContactoRecibido`).
4. Admin ve los mensajes en `/admin/mensajes-contacto` con badge de "no leídos".

---

## 9. Aspectos transversales

### 9.1. Validación doble (frontend + backend)

- **Frontend**: Reactive Forms con validators (`Validators.required`, `Validators.email`, `Validators.minLength`, custom). Bloquea el envío y muestra mensajes inmediatamente, sin trip al servidor.
- **Backend**: Form Requests con las **mismas reglas**. Es la fuente de verdad: aunque alguien hackee el frontend, no podrá saltarse la validación.

**Lección importante**: la validación de cliente es para UX, la de servidor es para seguridad. Las dos son necesarias.

### 9.2. Respuesta API uniforme

Todas las respuestas tienen la forma `{ success, data, message }`. Las paginadas siguen la forma estándar de Laravel (`current_page`, `data[]`, `total`, ...). Esto se traduce a:

- Un único interface `RespuestaApi<T>` en TypeScript.
- Un único patrón de manejo de errores en el frontend: leer `error.error.message` y mostrarlo.

### 9.3. Internacionalización

`LOCALE_ID: 'es-ES'` global. Los pipes (`{{ precio | number:'1.2-2' }}`, `{{ fecha | date:'longDate' }}`) usan formato español: punto como separador de miles, coma como decimal, fechas en español ("15 de agosto de 2026").

### 9.4. Responsive design

CSS escrito mobile-first con media queries. Uso de Flexbox y Grid. El menú de la navbar y del panel cliente se colapsa a hamburguesa en móvil. El portfolio cambia de 3 columnas (desktop) a 2 (tablet) a 1 (móvil).

### 9.5. Accesibilidad básica

- `alt` en todas las imágenes.
- `<label for="...">` asociado a cada input.
- `aria-label` en botones que solo tienen icono.
- Contraste de colores cumple WCAG AA (dorado `#C9A961` sobre beige `#F5EFE6` está validado).

### 9.6. Seguridad

| Riesgo | Mitigación |
| --- | --- |
| **SQL injection** | Eloquent escapa parámetros automáticamente (PDO prepared statements). Nunca uso `DB::raw` con input del usuario. |
| **XSS** | Angular escapa por defecto cualquier interpolación `{{ }}`. Solo se inyecta HTML con `[innerHTML]` y los datos vienen de la BD (controlados por el admin). |
| **CSRF** | Las APIs públicas no tienen estado de sesión, usan tokens Bearer. CSRF aplica solo a formularios con cookies. |
| **Mass assignment** | Cada modelo tiene `$fillable` que limita qué campos se pueden asignar masivamente. |
| **Password en logs** | El array `$hidden` excluye `password` y `remember_token` de las respuestas JSON. |
| **Brute force login** | Laravel + Sanctum incluyen rate limiting por IP. |
| **Tokens robados** | En reset password se invalidan todos los tokens existentes. Logout borra el token concreto. |
| **Email enumeration** | Recuperación de contraseña responde siempre OK. |
| **Roles** | Doble check: middleware en backend + guard en frontend. El backend es la fuente de verdad. |

---

## 10. Cómo levantar y demostrar el proyecto

### 10.1. Pre-requisitos

- Docker Desktop 24+ instalado y corriendo.
- Puertos libres: 4200, 8000, 3306, 8080, 8025, 1025.

### 10.2. Arranque inicial

```bash
cd ProyectoIntermodular
docker compose up -d
docker compose exec backend php artisan migrate --seed
```

El primer arranque tarda unos minutos (descarga imágenes y compila dependencias). Los siguientes son inmediatos.

### 10.3. URLs de demostración

- **Frontend**: http://localhost:4200
- **API backend**: http://localhost:8000/api
- **phpMyAdmin**: http://localhost:8080 (root / rootpass) — para enseñar la BD al tribunal.
- **Mailhog**: http://localhost:8025 — para enseñar los emails de verificación.

### 10.4. Credenciales de demo

- **Admin**: `admin@weddingplanner.com` / `admin1234`
- **Cliente con boda pendiente**: `lucia@example.com` / `cliente1234`
- **Cliente con boda activa**: `carlos@example.com` / `cliente1234`

### 10.5. Guion de demostración (15 minutos)

Si tienes que demostrar al tribunal, sigue este guion:

1. **Landing pública** (2 min): scroll por todas las secciones, mostrar animaciones, abrir un modal del portfolio, ver el carrusel de testimonios.
2. **Formulario de contacto** (1 min): rellenar y mostrar que llega a Mailhog. Mostrar también que aparece en `/admin/mensajes-contacto`.
3. **Registro** (2 min): crear un nuevo cliente. Mostrar el email en Mailhog, hacer click en "Confirmar".
4. **Cuestionario inicial** (2 min): rellenar los 4 pasos. Mostrar que después se queda en `/panel/pendiente`.
5. **Activación por admin** (2 min): logout, login como admin, ir a Bodas, activar la boda recién creada. Mostrar dashboard con estadísticas.
6. **Panel cliente completo** (4 min): logout y login con `carlos@example.com` (que ya tiene boda activa).
   - Personalización: marcar/desmarcar proveedores, ver presupuesto cambiar.
   - Mesas: crear una mesa, crear invitados, arrastrarlos.
   - Chat: enviar un mensaje. Hacer login como admin en otra ventana y mostrar que llega.
7. **Cierre** (2 min): mostrar phpMyAdmin con las tablas en español, abrir docker-compose y comentar la arquitectura.

---

## 11. Posibles preguntas del tribunal y respuestas

### Sobre arquitectura

**Q: ¿Por qué SPA con API REST y no Blade clásico?**
A: Para separar responsabilidades, permitir reutilizar la API en el futuro (app móvil, integración con CRM…) y demostrar que sé manejar dos tecnologías independientes. También facilita el despliegue: el frontend se sirve desde nginx, el backend escala como microservicio si hace falta.

**Q: ¿Por qué no microservicios?**
A: Microservicios añaden complejidad operacional enorme (despliegue independiente, comunicación entre servicios, observabilidad…) que no compensa para el tamaño del problema. Una API REST monolítica con secciones bien organizadas es lo correcto. La regla es: empieza monolito, divide cuando duela.

### Sobre seguridad

**Q: ¿Qué pasa si alguien manipula el `localStorage` y se pone `rol: administrador`?**
A: No pasa nada peligroso. El frontend mostraría enlaces de admin en la navbar, pero al pulsarlos llamaría a la API y el middleware `es.administrador` devolvería 403. La fuente de verdad de la autorización es el backend, no el frontend. El localStorage solo es una caché de UX.

**Q: ¿Por qué tokens y no JWT?**
A: Sanctum genera tokens opacos guardados en BD. Ventaja sobre JWT: puedo **revocar** un token en cualquier momento (al hacer logout o reset password) borrando la fila. Con JWT, hasta que caduca, sigue siendo válido aunque cierres sesión. Para una SPA con sesiones largas, Sanctum es más seguro.

**Q: ¿Y si el token caduca mientras navega?**
A: El interceptor recibe 401, fuerza logout local y redirige a `/login`. Buena UX y seguro.

### Sobre Angular

**Q: ¿Qué son los signals y por qué usarlos?**
A: Signals son una API de estado reactivo introducida en Angular 16. Comparado con Observables de RxJS, los signals son **síncronos** (lees el valor con `signal()`) y los `computed` se recalculan automáticamente cuando cambian sus dependencias. Para estado global simple (usuario logueado, boda actual) son perfectos. Sigo usando RxJS para flujos asíncronos como el polling del chat.

**Q: ¿Por qué standalone components y no NgModules?**
A: Es lo recomendado oficialmente desde Angular 17. Los componentes declaran sus dependencias localmente, el bundle es más pequeño (mejor tree-shaking) y desaparece todo el boilerplate de los módulos.

**Q: ¿Por qué lazy loading?**
A: Para que un visitante anónimo solo descargue el código de la landing (~200 KB) en lugar de todo el panel admin. Mejora dramáticamente el First Contentful Paint.

### Sobre Laravel

**Q: ¿Qué es Eloquent y por qué?**
A: Es el ORM de Laravel. Convierte tablas en clases PHP con métodos para consultas (`Boda::where('estado', 'activa')->get()`), relaciones expresivas (`$boda->proveedores`), eventos (`creating`, `updating`...) y casts automáticos. Evita escribir SQL a mano para el 95% de los casos y previene SQL injection con PDO prepared statements.

**Q: ¿Cómo manejas las migraciones en producción?**
A: `php artisan migrate` aplica solo las migraciones nuevas (Laravel mantiene una tabla `migrations` con las ejecutadas). Para revertir hay `php artisan migrate:rollback`. En producción nunca se usa `migrate:fresh` (borra todo). Si una migración tiene un error grave, se hace una migración nueva que arregla el estado.

### Sobre Docker

**Q: ¿Por qué Docker?**
A: Tres razones: (1) reproducibilidad — cualquier máquina con Docker ejecuta el proyecto idéntico; (2) cero conflictos de versiones — ¿PHP 8.3 mientras tu equipo usa 8.1? Da igual, va dentro del contenedor; (3) preparación para producción real — el mismo Dockerfile sirve para deploy en AWS, Azure, DigitalOcean...

**Q: ¿Qué diferencia hay entre el target development y production del frontend?**
A: Development arranca `ng serve` con hot reload (puerto 4200, código fuente accesible). Production hace `ng build --configuration=production` (minificado, tree-shaking, AOT), copia los estáticos a una imagen `nginx:alpine` y los sirve por puerto 80. Se construye con `docker build --target production`.

### Sobre la BD

**Q: ¿Por qué MySQL y no PostgreSQL?**
A: Es la BD que estudiamos. PostgreSQL tiene ventajas técnicas (mejor soporte de JSON, tipos avanzados), pero para un modelo relacional clásico como este no compensa. MySQL 8 incluye soporte de JSON suficiente, transacciones ACID, y mejor compatibilidad con phpMyAdmin para la demo.

**Q: ¿Qué pasa con la integridad referencial?**
A: Todas las claves foráneas tienen `cascadeOnDelete()`: si elimino un cliente, su boda, sus mesas, sus invitados, sus mensajes y la fila pivote `boda_proveedor` se borran. Sin huérfanos.

**Q: ¿Cómo evitas duplicar emails?**
A: Constraint `UNIQUE` en `usuarios.email` + validación `unique:usuarios,email` en `RegistroRequest`. Doble defensa.

### Sobre la lógica de negocio

**Q: ¿Por qué bloquear el panel hasta que el admin active?**
A: Porque modela el flujo real del negocio: una boda no se contrata online, se contrata en una reunión presencial. La fase digital es para que el cliente, una vez contratado, agilice las decisiones internas (qué floristería de las que colaboran con la agencia prefiere). Es una herramienta interna de la agencia, no un marketplace.

**Q: ¿Por qué un solo admin? ¿Y si la agencia crece?**
A: El sistema soporta múltiples administradores: el campo `rol = 'administrador'` en `usuarios` es genérico. Para chats, actualmente envío al "primer admin disponible", pero podría asignar wedding planner a cada boda con un campo `wedding_planner_id`. Es una mejora directa sin reescribir nada.

---

## 12. Limitaciones conscientes y posibles mejoras

### 12.1. Limitaciones que asumí

- **Chat con polling, no WebSockets**: simple, suficiente, retrasa los mensajes hasta 7 segundos.
- **Un solo admin para todas las conversaciones**: el modelo lo soporta, no lo expuse en UI.
- **No hay subida de archivos al chat**: solo texto.
- **Imágenes del portfolio gestionadas por URL**, no upload directo desde el panel.
- **No hay paginación en el listado de invitados** (asumo bodas <500 invitados).

### 12.2. Mejoras que añadiría con más tiempo

1. **Notificaciones push (browser)** cuando llega un mensaje de chat.
2. **Subida de imágenes** al portfolio y como avatar de testimonios (Laravel Media Library).
3. **Asignación de wedding planner a cada boda** (campo `wedding_planner_id` en `bodas`).
4. **WebSockets con Laravel Reverb** (sustituiría el polling por canales en tiempo real).
5. **Tests automatizados** (PHPUnit en backend, Vitest en frontend).
6. **CI/CD con GitHub Actions**: tests + build de imágenes Docker en cada push.
7. **Despliegue real** (AWS ECS o DigitalOcean Apps).
8. **Internacionalización completa** (en/es) con `@angular/localize`.
9. **Modo oscuro** con CSS variables.
10. **Auditoría de cambios** (paquete `spatie/laravel-activitylog` para saber quién cambió qué y cuándo).

---

## Cierre

Este proyecto demuestra que sé:

- Diseñar una **arquitectura cliente-servidor moderna** (SPA + API REST).
- Modelar una **base de datos relacional** con integridad referencial.
- Implementar **autenticación y autorización** con tokens, verificación de email y roles.
- Desarrollar un **frontend reactivo** con Angular 21 (signals, standalone, lazy loading, drag & drop).
- Desarrollar un **backend RESTful** con Laravel 11 (Eloquent, Sanctum, Form Requests, Notifications, middlewares custom).
- **Empaquetar todo con Docker** para que sea reproducible y desplegable.
- **Validar en dos capas** (cliente y servidor) por seguridad.
- **Documentar y testear** el código de manera profesional.
- Tomar **decisiones técnicas justificadas**: cada elección tiene una razón, no es por moda.

El producto final es una aplicación web completa, profesional, funcional al 100%, lista para una agencia real con ajustes mínimos.
