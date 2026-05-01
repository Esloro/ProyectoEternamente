import { Component, OnInit, inject, signal } from '@angular/core';
import { DatePipe } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';

import { AdminService } from '../../../servicios/admin.service';
import { Boda, EstadoBoda, ETIQUETAS_ESTADO } from '../../../modelos';

@Component({
  selector: 'app-bodas',
  standalone: true,
  imports: [RouterLink, DatePipe],
  templateUrl: './bodas.html',
  styleUrl: './bodas.scss',
})
export class Bodas implements OnInit {
  private adminService = inject(AdminService);
  private route = inject(ActivatedRoute);

  protected bodas = signal<Boda[]>([]);
  protected cargando = signal(true);
  protected filtroEstado = signal<EstadoBoda | ''>('');
  protected busqueda = signal('');

  protected readonly etiquetasEstado = ETIQUETAS_ESTADO;
  protected readonly estados: { valor: EstadoBoda | ''; etiqueta: string }[] = [
    { valor: '', etiqueta: 'Todos los estados' },
    { valor: 'pendiente_reunion', etiqueta: 'Pendiente reunión' },
    { valor: 'activa', etiqueta: 'Activa' },
    { valor: 'finalizada', etiqueta: 'Finalizada' },
    { valor: 'cancelada', etiqueta: 'Cancelada' },
  ];

  private readonly estadosValidos: EstadoBoda[] = [
    'pendiente_reunion', 'activa', 'finalizada', 'cancelada',
  ];

  ngOnInit(): void {
    this.route.queryParamMap.subscribe((params) => {
      const estado = params.get('estado') ?? '';
      this.filtroEstado.set(
        this.estadosValidos.includes(estado as EstadoBoda) ? (estado as EstadoBoda) : '',
      );
      this.cargar();
    });
  }

  private cargar(): void {
    this.cargando.set(true);
    const filtros: { estado?: EstadoBoda; q?: string } = {};
    const estado = this.filtroEstado();
    if (estado) filtros.estado = estado;
    const q = this.busqueda().trim();
    if (q) filtros.q = q;

    this.adminService.bodas(filtros).subscribe({
      next: (r) => {
        this.bodas.set(r.data.data ?? []);
        this.cargando.set(false);
      },
      error: () => this.cargando.set(false),
    });
  }

  protected cambiarFiltro(estado: EstadoBoda | ''): void {
    this.filtroEstado.set(estado);
    this.cargar();
  }

  protected onBusqueda(event: Event): void {
    this.busqueda.set((event.target as HTMLInputElement).value);
    this.cargar();
  }
}
