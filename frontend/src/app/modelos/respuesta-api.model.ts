/**
 * Forma estandar de cualquier respuesta de la API REST del backend.
 * El backend siempre devuelve { success, data, message }.
 */
export interface RespuestaApi<T = unknown> {
  success: boolean;
  data: T;
  message: string;
}

/**
 * Forma de las respuestas paginadas de Laravel.
 */
export interface RespuestaPaginada<T> {
  current_page: number;
  data: T[];
  first_page_url: string;
  from: number;
  last_page: number;
  last_page_url: string;
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number;
  total: number;
}
