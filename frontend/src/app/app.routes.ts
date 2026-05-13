import { Routes } from '@angular/router';
import { authGuard } from './guardianes/auth.guard';
import { adminGuard } from './guardianes/admin.guard';

export const routes: Routes = [
  // Landing pública
  {
    path: '',
    loadComponent: () => import('./paginas/landing/landing').then((m) => m.Landing),
  },

  // Términos y condiciones
  {
    path: 'terminos-condiciones',
    loadComponent: () =>
      import('./paginas/terminos-condiciones/terminos-condiciones').then(
        (m) => m.TerminosCondiciones,
      ),
  },

  // Autenticación
  {
    path: 'login',
    loadComponent: () => import('./paginas/login/login').then((m) => m.Login),
  },
  {
    path: 'registro',
    loadComponent: () => import('./paginas/registro/registro').then((m) => m.Registro),
  },
  {
    path: 'recuperar-password',
    loadComponent: () =>
      import('./paginas/recuperar-password/recuperar-password').then((m) => m.RecuperarPassword),
  },
  {
    path: 'email-verificado',
    loadComponent: () =>
      import('./paginas/email-verificado/email-verificado').then((m) => m.EmailVerificado),
  },

  // Panel cliente (lazy children)
  {
    path: 'panel',
    canActivate: [authGuard],
    loadChildren: () =>
      import('./paginas/panel-cliente/panel-cliente.routes').then((m) => m.PANEL_CLIENTE_ROUTES),
  },

  // Panel Admin (lazy children)
  {
    path: 'admin',
    canActivate: [authGuard, adminGuard],
    loadChildren: () =>
      import('./paginas/panel-admin/panel-admin.routes').then((m) => m.PANEL_ADMIN_ROUTES),
  },

  // Cualquier ruta no listada → landing
  {
    path: '**',
    redirectTo: '',
  },
];
