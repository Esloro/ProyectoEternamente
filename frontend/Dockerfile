# =====================================================================
# Frontend - Angular 21
# Multi-stage Dockerfile: target `development` para `ng serve` con
# hot reload, target `production` para servir el build con nginx.
# =====================================================================

# ---------- Stage: development ----------
FROM node:22-alpine AS development

WORKDIR /app

# Angular CLI 21 preinstalado para poder ejecutar `ng generate` dentro
# del contenedor sin instalarlo en cada arranque.
RUN npm install -g @angular/cli@21

EXPOSE 4200

# Si el proyecto Angular aún no se ha inicializado (paso 4) el contenedor
# se queda esperando para no caer en bucle de reinicio. Una vez exista
# package.json, instala dependencias y arranca `ng serve` accesible
# desde el host (--host 0.0.0.0) y con polling para que el watch
# funcione en bind mounts de Windows.
CMD ["sh", "-c", "if [ -f package.json ]; then npm install && npm start -- --host 0.0.0.0 --port 4200 --poll 2000; else echo '>> Angular aún no inicializado. Ejecuta los comandos del README (paso 4) para crear el proyecto.'; tail -f /dev/null; fi"]


# ---------- Stage: build (compila para producción) ----------
FROM node:22-alpine AS build

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build -- --configuration=production


# ---------- Stage: production (sirve estáticos con nginx) ----------
FROM nginx:alpine AS production

# Copiamos el build Angular a la raíz de nginx. El glob */browser
# cubre la salida estándar de `ng build` en Angular 17+.
COPY --from=build /app/dist/*/browser/ /usr/share/nginx/html/
COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
