import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AutenticacionService } from '../servicios/autenticacion.service';

/**
 * Bloquea el acceso a las secciones administrativas si el usuario no
 * tiene rol "administrador". Si no esta logueado, redirige a /login;
 * si esta logueado pero es cliente, redirige a su panel.
 */
export const adminGuard: CanActivateFn = () => {
  const auth = inject(AutenticacionService);
  const router = inject(Router);

  if (!auth.estaLogueado()) {
    return router.createUrlTree(['/login']);
  }
  if (!auth.esAdministrador()) {
    return router.createUrlTree(['/panel']);
  }
  return true;
};
