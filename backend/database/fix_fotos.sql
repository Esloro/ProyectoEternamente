-- =====================================================================
-- fix_fotos.sql
-- ---------------------------------------------------------------------
-- Actualiza las URLs de fotos en la BD del droplet para que apunten al
-- frontend (Vercel) en lugar de al backend (Laravel/storage).
--
-- Las imagenes se han movido a frontend/public/bodas/ y se sirven desde
-- Vercel. Por eso aqui dejamos rutas relativas tipo /bodas/xxx.jpg, que
-- el navegador resuelve contra el dominio del frontend.
--
-- Uso en el droplet:
--   docker compose -f docker-compose.prod.yml exec -T mysql \
--     mysql -u root -p$DB_ROOT_PASSWORD $DB_DATABASE < backend/database/fix_fotos.sql
--
-- O abriendo una shell en mysql y pegando el contenido a mano.
-- =====================================================================

-- Convierte cualquier URL que termine en /storage/bodas/<archivo> en /bodas/<archivo>.
-- SUBSTRING_INDEX(foto, '/storage/bodas/', -1) extrae lo que hay despues de /storage/bodas/.

UPDATE portfolio
SET    foto = CONCAT('/bodas/', SUBSTRING_INDEX(foto, '/storage/bodas/', -1))
WHERE  foto LIKE '%/storage/bodas/%';

UPDATE testimonios
SET    foto = CONCAT('/bodas/', SUBSTRING_INDEX(foto, '/storage/bodas/', -1))
WHERE  foto LIKE '%/storage/bodas/%';

UPDATE proveedores
SET    foto = CONCAT('/bodas/', SUBSTRING_INDEX(foto, '/storage/bodas/', -1))
WHERE  foto LIKE '%/storage/bodas/%';

-- Comprobacion: muestra las primeras 5 filas de cada tabla tras la actualizacion.
SELECT 'portfolio'   AS tabla, id, foto FROM portfolio   LIMIT 5;
SELECT 'testimonios' AS tabla, id, foto FROM testimonios LIMIT 5;
SELECT 'proveedores' AS tabla, id, foto FROM proveedores LIMIT 5;
