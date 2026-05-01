import { Routes } from '@angular/router';

export const PANEL_ADMIN_ROUTES: Routes = [
  {
    path: '',
    loadComponent: () => import('./panel-admin').then((m) => m.PanelAdmin),
    children: [
      { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
      {
        path: 'dashboard',
        loadComponent: () => import('./dashboard/dashboard').then((m) => m.Dashboard),
      },
      {
        path: 'clientes',
        loadComponent: () => import('./clientes/clientes').then((m) => m.Clientes),
      },
      {
        path: 'bodas',
        loadComponent: () => import('./bodas/bodas').then((m) => m.Bodas),
      },
      {
        path: 'bodas/:id',
        loadComponent: () => import('./bodas/boda-detalle/boda-detalle').then((m) => m.BodaDetalle),
      },
      {
        path: 'proveedores',
        loadComponent: () => import('./catalogo/proveedores/proveedores').then((m) => m.ProveedoresAdmin),
      },
      {
        path: 'paquetes',
        loadComponent: () => import('./catalogo/paquetes/paquetes').then((m) => m.PaquetesAdmin),
      },
      {
        path: 'portfolio',
        loadComponent: () => import('./catalogo/portfolio/portfolio').then((m) => m.PortfolioAdmin),
      },
      {
        path: 'testimonios',
        loadComponent: () => import('./catalogo/testimonios/testimonios').then((m) => m.TestimoniosAdmin),
      },
      {
        path: 'mensajes-contacto',
        loadComponent: () =>
          import('./mensajes-contacto/mensajes-contacto').then((m) => m.MensajesContactoAdmin),
      },
      {
        path: 'chat',
        loadComponent: () => import('./chat-admin/chat-admin').then((m) => m.ChatAdmin),
      },
      {
        path: 'chat/:bodaId',
        loadComponent: () =>
          import('./chat-admin/chat-admin-boda/chat-admin-boda').then((m) => m.ChatAdminBoda),
      },
    ],
  },
];
