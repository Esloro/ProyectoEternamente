# PROMPT PARA CLAUDE CODE — PROYECTO WEDDING PLANNER (TFG 2º DAW)

---

## 🎯 CONTEXTO DEL PROYECTO

Necesito que desarrolles una **aplicación web completa para una agencia de Wedding Planner** (organización de bodas). Es mi **Trabajo de Fin de Grado de 2º DAW**, por lo que debe ser:

- **Bonita y profesional** (es el escaparate del negocio).
- **Funcional al 100%** (todo debe funcionar de verdad, no ser un mockup).
- **Simple en cuanto a tecnología** (sin librerías rebuscadas ni patrones avanzados de empresa).
- **Lista para producción con Docker**.

**Nivel esperado:** código limpio, comentado, comprensible para un alumno de 2º DAW. Nada de arquitecturas hexagonales, DDD, microservicios ni cosas similares.

---

## 🧰 STACK TECNOLÓGICO OBLIGATORIO

- **Frontend:** Angular 21 (standalone components, signals si es natural usarlas, sin NgRx ni librerías de estado complejas).
- **Backend:** Laravel 11 (API REST con Sanctum para autenticación por tokens).
- **Base de datos:** MySQL 8.
- **Contenedores:** Docker + Docker Compose (servicios: `frontend`, `backend`, `mysql`, `phpmyadmin`, `mailhog` para probar emails en local).
- **Estilos:** SCSS puro en Angular (sin Tailwind, sin Bootstrap). Quiero CSS hecho a mano, bonito y aprendible.
- **Animaciones:** CSS transitions, `@keyframes` y Angular Animations (`@angular/animations`). Algo elegante, no recargado: fades, slide-ins, hover suaves, parallax ligero en la landing.
- **Iconos:** una librería simple como `lucide-angular` o SVG inline.
- **Email:** Laravel Mail con driver SMTP apuntando a Mailhog en desarrollo.

---

## 🎨 DISEÑO Y ESTÉTICA

**Paleta de colores (tonos neutros, elegantes, estilo boda):**

- Beige claro de fondo: `#F5EFE6`
- Beige medio: `#E8DFCA`
- Dorado principal: `#C9A961`
- Dorado oscuro (hover): `#A8893F`
- Gris oscuro para texto: `#3D3D3D`
- Gris medio: `#7A7A7A`
- Blanco roto: `#FAF7F2`

**Tipografía:**

- Títulos: **Playfair Display** (serif elegante, Google Fonts).
- Texto: **Lato** o **Montserrat** (sans-serif limpio).

**Estilo general:** minimalista, mucho espacio en blanco, fotografías grandes, tipografía serif en títulos para dar sensación premium. Inspiración: webs de wedding planners reales como The Knot, Zankyou, etc.

---

## 🌐 LANDING PAGE (pública, sin login)

Una sola página con scroll, dividida en estas secciones (en este orden):

1. **Navbar fijo** (con efecto de fondo translúcido al hacer scroll):
   - Logo a la izquierda.
   - Enlaces: *Inicio · Paquetes · Portfolio · Testimonios · Sobre Nosotros · Contacto*.
   - A la derecha: botones **Iniciar Sesión** y **Registrarse** (este último destacado en dorado).

2. **Hero principal:**
   - Imagen grande de fondo (boda elegante) con overlay oscuro suave.
   - Título grande tipo *"Hacemos realidad la boda de tus sueños"*.
   - Subtítulo y un botón CTA *"Descubre nuestros paquetes"* que haga scroll hasta la sección de paquetes.
   - Animación de fade-in al cargar.

3. **Sección Paquetes:** tres tarjetas centradas:
   - **Solo Asesoramiento** (precio orientativo).
   - **Paquete Base** (más popular, badge dorado).
   - **Paquete Premium**.
   - Cada tarjeta lista qué incluye con iconos de check.
   - Botón *"Contratar"* en cada una → si no está logueado, redirige a `/registro`.

4. **Portfolio:** grid responsive (3 columnas en desktop, 2 en tablet, 1 en móvil) con fotos de bodas anteriores. Al pasar el ratón: zoom suave + overlay con el nombre de la boda. Click → modal con galería.

5. **Testimonios:** carrusel horizontal con 3-5 opiniones de clientes (foto, nombre, estrellas, texto). Auto-play cada 5s, con flechas manuales.

6. **Sobre Nosotros:** dos columnas — foto del equipo a un lado, texto con la historia de la agencia al otro.

7. **Contacto:**
   - Formulario simple (nombre, email, teléfono, mensaje) que guarda en BD y envía email.
   - Datos de contacto: dirección, teléfono, email, redes sociales.
   - Mapa embebido (iframe de Google Maps con la ubicación).

8. **Footer:** logo, enlaces rápidos, redes sociales, copyright.

**Animaciones esperadas:** scroll-reveal en cada sección (elementos que aparecen al hacer scroll), hover suaves en botones y tarjetas, transiciones de página.

---

## 🔐 SISTEMA DE AUTENTICACIÓN

- **Registro de cliente:** formulario con nombre, apellidos, email, teléfono, contraseña, confirmar contraseña.
- Tras registrarse: **email de confirmación** con enlace de verificación (Laravel ya trae esto con `MustVerifyEmail`).
- **Login** con email + contraseña, devuelve token Sanctum.
- **Recuperar contraseña:** "He olvidado mi contraseña" → email con token → formulario de nueva contraseña.
- **Logout** que invalida el token.
- **Roles:** `cliente` y `administrador` (campo `rol` en tabla `usuarios`). El admin se crea por seeder, no por registro público.
- Guards en Angular: `authGuard` y `adminGuard`.

---

## 📋 CUESTIONARIO INICIAL (tras registrarse)

Importante: el cliente **NO debe acceder al panel completo solo por registrarse**. La idea es:

1. Cliente se registra → confirma email → entra.
2. Rellena un **cuestionario inicial** con los datos básicos de su boda:
   - Tipo de ceremonia: civil / iglesia / aire libre / otra.
   - Si es iglesia: cuál.
   - Fecha aproximada de la boda.
   - Número estimado de invitados.
   - Franja horaria: mañana / tarde / noche.
   - Estilo/temática preferida: clásica / rústica / moderna / boho / glamour.
   - Tipo de comida: cóctel / banquete / buffet / familiar.
   - Presupuesto orientativo (rangos).
3. Al guardar, se crea su `boda` en estado **"pendiente_reunion"**.
4. Ve un mensaje: *"Hemos recibido tus datos. Nos pondremos en contacto contigo para una reunión inicial. Una vez confirmada la contratación, podrás acceder a todas las opciones de personalización de tu boda."*
5. **El administrador** desde su panel cambia el estado a **"activa"** una vez se ha hecho la reunión y se ha contratado el servicio. Solo entonces el cliente desbloquea el panel completo.

Esto soluciona tu duda de cómo justificar que el negocio tenga sentido: **el acceso al panel completo lo desbloquea el admin manualmente**.

---

## 👰 PANEL DEL CLIENTE (una vez la boda está activa)

Menú lateral con secciones:

1. **Resumen de mi boda:** datos generales + estado + presupuesto actual estimado + cuenta atrás hasta la fecha.

2. **Personalización (selección de proveedores y opciones):** secciones tipo formulario/galería para elegir:
   - **Lugar de celebración** (lista de fincas/restaurantes con foto y precio).
   - **Floristería** (ramos, centros de mesa, decoración).
   - **Música/DJ** (DJ, grupo en vivo, música clásica…).
   - **Fotografía y vídeo** (paquetes con distintos fotógrafos).
   - **Catering / Menú** (opciones según tipo elegido).
   - **Decoración y temática** (estilos visuales con fotos).
   - **Vestuario / Trajes** (asesoramiento).
   - **Coches / Transporte**.
   - **Detalles para invitados** (recordatorios).
   - **Tarta nupcial**.

   Cada sección muestra opciones tipo tarjeta con foto, descripción y precio. El cliente marca lo que quiere y al guardar se actualiza el **presupuesto estimado** automáticamente.

3. **Organización de mesas (sencilla):**
   - Pantalla con un grid de mesas (se elige número de mesas y comensales por mesa).
   - Lista de invitados a la izquierda (con CRUD simple: nombre, alergias, acompañante).
   - Drag & drop simple usando `@angular/cdk/drag-drop` para arrastrar invitados a las mesas.
   - Vista visual con mesas redondas dibujadas en CSS.

4. **Presupuesto:** desglose detallado de todo lo elegido + total estimado. Botón "Solicitar presupuesto definitivo" → notifica al admin.

5. **Chat con la wedding planner:** chat interno simple (ver más abajo).

6. **Mi perfil:** editar datos personales y contraseña.

---

## 🛠️ PANEL DEL ADMINISTRADOR

Menú lateral con:

1. **Dashboard:** estadísticas (nº de bodas activas, pendientes, ingresos estimados, próximas bodas).

2. **Clientes:** listado, búsqueda, ver detalle de cada cliente y su boda.

3. **Bodas:** listado de todas las bodas con filtros por estado (pendiente_reunion, activa, finalizada, cancelada). Click en una → ve todas las elecciones, presupuesto, invitados, mesas, etc. Puede **cambiar el estado** y **ajustar/confirmar el presupuesto definitivo**.

4. **Proveedores:** CRUD de las opciones que ve el cliente (floristerías, DJs, fotógrafos…). Cada proveedor tiene: nombre, categoría, descripción, foto, precio.

5. **Paquetes:** CRUD de los 3 paquetes de la landing (por si se cambian precios).

6. **Portfolio:** CRUD de las fotos del portfolio público.

7. **Testimonios:** CRUD de testimonios (con campo `verificado` boolean).

8. **Mensajes de contacto:** ver los mensajes recibidos del formulario público.

9. **Chat:** lista de conversaciones con clientes.

---

## 💬 CHAT INTERNO

Implementación **simple**, sin WebSockets:

- Tabla `mensajes` (id, boda_id, emisor_id, receptor_id, contenido, leido, fecha).
- Polling cada 5-10 segundos desde Angular (con `interval` de RxJS).
- Interfaz tipo WhatsApp básica: lista de conversaciones a la izquierda, chat a la derecha, input abajo.
- Indicador de mensajes no leídos.

(Sin WebSockets ni Pusher para no complicar el TFG. Si te sobra tiempo, deja preparado para añadirlo.)

---

## 🗄️ BASE DE DATOS (MySQL) — TODO EN ESPAÑOL

Tablas mínimas (nombres de tablas, columnas, modelos y relaciones **en español**):

- `usuarios` (id, nombre, apellidos, email, telefono, password, rol, email_verificado_en, created_at, updated_at).
- `bodas` (id, usuario_id, tipo_ceremonia, iglesia, fecha_boda, num_invitados, franja_horaria, tematica, tipo_comida, presupuesto_orientativo, estado, presupuesto_estimado, presupuesto_definitivo, created_at, updated_at).
- `proveedores` (id, nombre, categoria, descripcion, foto, precio, activo).
- `categorias_proveedores` (id, nombre, slug, icono) — opcional, puedes dejarlo como ENUM.
- `boda_proveedor` (boda_id, proveedor_id, notas) — pivote para las elecciones del cliente.
- `invitados` (id, boda_id, nombre, alergias, acompanante, mesa_id).
- `mesas` (id, boda_id, numero, capacidad).
- `mensajes` (id, boda_id, emisor_id, receptor_id, contenido, leido, created_at).
- `paquetes` (id, nombre, descripcion, precio, caracteristicas_json, destacado).
- `portfolio` (id, titulo, descripcion, foto, orden).
- `testimonios` (id, nombre_cliente, foto, valoracion, comentario, verificado).
- `mensajes_contacto` (id, nombre, email, telefono, mensaje, leido, created_at).

**Seeders obligatorios:**

- 1 administrador (`admin@weddingplanner.com` / `admin1234`).
- 2-3 clientes de prueba (uno con boda pendiente, otro con boda activa).
- ~15 proveedores variados de distintas categorías.
- 3 paquetes.
- 6-8 elementos en portfolio.
- 5 testimonios.

---

## 🎨 NOMENCLATURA EN ESPAÑOL

**Todo en español**, como pediste. Ejemplos:

- **Modelos Laravel:** `Usuario`, `Boda`, `Proveedor`, `Invitado`, `Mesa`, `Mensaje`, `Paquete`, `Portfolio`, `Testimonio`, `MensajeContacto`.
- **Controladores:** `UsuarioController`, `BodaController`, `AutenticacionController`, `ProveedorController`, etc.
- **Componentes Angular** (cada uno con su `.html`, `.ts` y `.scss` separados, **NO inline**):
  - `landing/`, `navbar/`, `hero/`, `paquetes/`, `portfolio/`, `testimonios/`, `contacto/`, `footer/`
  - `login/`, `registro/`, `recuperar-password/`
  - `cuestionario-inicial/`
  - `panel-cliente/`, `resumen-boda/`, `personalizacion/`, `organizador-mesas/`, `presupuesto/`, `chat/`, `mi-perfil/`
  - `panel-admin/`, `dashboard/`, `lista-clientes/`, `lista-bodas/`, `gestion-proveedores/`, etc.
- **Servicios Angular:** `autenticacion.service.ts`, `boda.service.ts`, `proveedor.service.ts`, `chat.service.ts`…
- **Variables y campos:** `email`, `password`, `nombre`, `apellidos`, `numInvitados`, `bodaId`, `proveedorId`, `tipoCeremonia`, `presupuestoEstimado`…

---

## 📁 ESTRUCTURA DE CARPETAS DEL PROYECTO

```
wedding-planner/
├── docker-compose.yml
├── README.md
├── frontend/                # Angular 21
│   ├── Dockerfile
│   ├── src/
│   │   └── app/
│   │       ├── componentes/
│   │       ├── servicios/
│   │       ├── modelos/
│   │       ├── guardianes/   (guards)
│   │       ├── interceptores/
│   │       └── paginas/
│   └── ...
└── backend/                 # Laravel 11
    ├── Dockerfile
    ├── app/
    │   ├── Models/
    │   ├── Http/Controllers/Api/
    │   ├── Mail/
    │   └── ...
    ├── database/
    │   ├── migrations/
    │   └── seeders/
    └── routes/api.php
```

---

## 🐳 DOCKER

`docker-compose.yml` con estos servicios:

- **`frontend`** (Angular servido con nginx en producción, o `ng serve` en dev) — puerto 4200.
- **`backend`** (PHP 8.3 + Laravel) — puerto 8000.
- **`mysql`** (MySQL 8) — puerto 3306, volumen persistente.
- **`phpmyadmin`** — puerto 8080.
- **`mailhog`** — puerto 8025 para ver emails en desarrollo.

`README.md` con instrucciones claras: `docker compose up -d`, migraciones, seeders, URLs y credenciales de prueba.

---

## ✅ REQUISITOS DE CALIDAD

1. **Validación tanto en frontend como en backend** (Reactive Forms + Form Requests de Laravel).
2. **Manejo de errores:** mensajes claros, no excepciones crudas. Toasts/notificaciones bonitas en Angular.
3. **Responsive:** todo debe verse bien en móvil, tablet y desktop.
4. **Accesibilidad básica:** etiquetas `alt`, labels asociadas, contraste correcto.
5. **Código comentado** en zonas relevantes, en español.
6. **API REST** bien estructurada bajo `/api/...` con respuestas JSON consistentes (`{ success, data, message }`).
7. **Interceptor en Angular** que añada el token Bearer y maneje 401 (redirigir a login).
8. **Testing manual:** una vez terminado, prueba todos los flujos críticos y dime si algo falla:
   - Registro → email de verificación → login.
   - Recuperar contraseña.
   - Cuestionario inicial → estado pendiente_reunion.
   - Admin activa la boda → cliente desbloquea panel.
   - Personalización + cálculo de presupuesto.
   - Organizador de mesas con drag & drop.
   - Chat funcionando con polling.
   - Formulario de contacto envía email y guarda en BD.

---

## 🚀 ENTREGABLES FINALES

1. Proyecto completo con frontend, backend y BD funcionando con un solo `docker compose up`.
2. Seeders ejecutados con datos de prueba realistas.
3. README detallado con: requisitos previos, instalación paso a paso, URLs, credenciales de admin y de cliente de prueba, comandos útiles.
4. Lista de **endpoints de la API** en el README.
5. Resumen de qué has probado y qué funciona al final.

---

## 📌 INSTRUCCIONES DE EJECUCIÓN PARA TI (CLAUDE CODE)

- Construye el proyecto **paso a paso**, no todo de golpe. Sigue este orden:
  1. Estructura de carpetas + Docker Compose + Dockerfiles.
  2. Backend Laravel: migraciones, modelos, seeders, autenticación con Sanctum y verificación de email.
  3. Endpoints de la API (autenticación, boda, proveedores, chat, contacto, admin).
  4. Frontend Angular: estructura, rutas, servicios, guards, interceptor.
  5. Landing page completa con todas sus secciones y animaciones.
  6. Flujo de registro/login/recuperar password.
  7. Cuestionario inicial y panel cliente.
  8. Panel admin.
  9. Chat con polling.
  10. Pruebas manuales y correcciones.
- Después de cada bloque grande, hazme un **resumen breve** de lo que has hecho y qué viene a continuación.
- Si tienes dudas razonables (ej. precios concretos, copys exactos, fotos a usar), **decide tú** con criterio sensato y sigue. Usa imágenes de Unsplash con URLs públicas para las fotos.
- **No uses librerías innecesarias.** Si dudas entre dos opciones, elige siempre la más simple.
- Al final, ejecuta los flujos críticos y reporta resultados.

---

**Empieza ya con el paso 1 (estructura + Docker) y vamos avanzando.**
