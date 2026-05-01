/**
 * Variables de entorno por defecto. Se sustituyen en build de produccion
 * via fileReplacements de angular.json.
 */
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api',
  // Intervalo de polling del chat (ms). El backend no usa WebSockets
  // para mantener el TFG simple.
  intervaloPollingChat: 7000,
};
