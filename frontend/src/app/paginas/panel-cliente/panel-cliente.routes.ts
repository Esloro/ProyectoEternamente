import { Routes } from '@angular/router';
import { bodaActivaGuard } from '../../guardianes/boda-activa.guard';

export const PANEL_CLIENTE_ROUTES: Routes = [
  {
    path: '',
    loadComponent: () => import('./panel-cliente').then((m) => m.PanelCliente),
    children: [
      { path: '', redirectTo: 'resumen', pathMatch: 'full' },
      {
        path: 'cuestionario',
        loadComponent: () =>
          import('./cuestionario-inicial/cuestionario-inicial').then((m) => m.CuestionarioInicial),
      },
      {
        path: 'pendiente',
        loadComponent: () =>
          import('./pendiente-reunion/pendiente-reunion').then((m) => m.PendienteReunion),
      },
      {
        path: 'resumen',
        loadComponent: () => import('./resumen-boda/resumen-boda').then((m) => m.ResumenBoda),
      },
      {
        path: 'personalizacion',
        canActivate: [bodaActivaGuard],
        loadComponent: () =>
          import('./personalizacion/personalizacion').then((m) => m.Personalizacion),
      },
      {
        path: 'mesas',
        canActivate: [bodaActivaGuard],
        loadComponent: () =>
          import('./organizador-mesas/organizador-mesas').then((m) => m.OrganizadorMesas),
      },
      {
        path: 'presupuesto',
        canActivate: [bodaActivaGuard],
        loadComponent: () => import('./presupuesto/presupuesto').then((m) => m.Presupuesto),
      },
      {
        path: 'chat',
        canActivate: [bodaActivaGuard],
        loadComponent: () =>
          import('./chat-cliente/chat-cliente').then((m) => m.ChatCliente),
      },
      {
        path: 'perfil',
        loadComponent: () => import('./mi-perfil/mi-perfil').then((m) => m.MiPerfil),
      },
    ],
  },
];
