import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { catchError, map, of } from 'rxjs';
import { BodaService } from '../servicios/boda.service';

/**
 * Restringe las secciones del panel que requieren que la boda este
 * en estado `activa`. Si la boda esta pendiente, redirige a la pagina
 * de "estamos en contacto contigo". Si no hay boda, al cuestionario.
 */
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
