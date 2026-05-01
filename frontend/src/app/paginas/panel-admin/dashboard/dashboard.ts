import { Component, OnInit, inject, signal } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { RouterLink } from '@angular/router';

import { AdminService } from '../../../servicios/admin.service';

interface ResumenDashboard {
  bodas: { pendiente_reunion: number; activas: number; finalizadas: number; canceladas: number };
  clientes_total: number;
  mensajes_no_leidos: number;
  chats_no_leidos: number;
  ingresos_estimados: number;
}

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [RouterLink, DecimalPipe],
  templateUrl: './dashboard.html',
  styleUrl: './dashboard.scss',
})
export class Dashboard implements OnInit {
  private adminService = inject(AdminService);

  protected datos = signal<ResumenDashboard | null>(null);
  protected cargando = signal(true);

  ngOnInit(): void {
    this.adminService.dashboard().subscribe({
      next: (r) => {
        this.datos.set(r.data as ResumenDashboard);
        this.cargando.set(false);
      },
      error: () => this.cargando.set(false),
    });
  }
}
