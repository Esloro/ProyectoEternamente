import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AutenticacionService } from '../servicios/autenticacion.service';

/**
 * Permite el paso solo si hay un usuario logueado. Si no, redirige a /login
 * conservando la URL solicitada como `returnUrl` para poder volver tras login.
 */
export const authGuard: CanActivateFn = (_ruta, estado) => {
  const auth = inject(AutenticacionService);
  const router = inject(Router);

  if (auth.estaLogueado()) {
    return true;
  }
  return router.createUrlTree(['/login'], { queryParams: { returnUrl: estado.url } });
};
