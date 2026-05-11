/**
 * Variables de entorno para el build de produccion.
 * Vercel sustituye este archivo en build via fileReplacements (angular.json).
 */
export const environment = {
  production: true,
  apiUrl: 'https://api.eternamente.tech/api',
  intervaloPollingChat: 7000,
};
