import { HttpErrorResponse, HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { catchError, throwError } from 'rxjs';
import { environment } from '../../environments/environment';
import { AutenticacionService } from '../servicios/autenticacion.service';

/**
 * Intercepta todas las llamadas HTTP que vayan a nuestra API:
 *   1. Añade `Authorization: Bearer <token>` si hay token guardado.
 *   2. Si la respuesta es 401, fuerza el cierre de sesion local y
 *      redirige a /login (el token ha caducado o no es valido).
 */
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
      // 401 = token invalido o ausente cuando se esperaba uno.
      // Solo cerramos sesion si HABIA token (asi no se rompe el flujo
      // de "intento de login con credenciales malas").
      if (error.status === 401 && token) {
        auth.forzarCierreLocal();
      }
      return throwError(() => error);
    }),
  );
};
