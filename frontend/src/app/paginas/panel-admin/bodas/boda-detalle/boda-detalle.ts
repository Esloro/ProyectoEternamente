import { Component, OnInit, inject, signal } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { DatePipe, DecimalPipe } from '@angular/common';
import { FormControl, ReactiveFormsModule, Validators } from '@angular/forms';

import { AdminService } from '../../../../servicios/admin.service';
import {
  Boda,
  EstadoBoda,
  ETIQUETAS_ESTADO,
  ETIQUETAS_CEREMONIA,
  ETIQUETAS_FRANJA,
  ETIQUETAS_TEMATICA,
  ETIQUETAS_COMIDA,
  ETIQUETAS_PRESUPUESTO,
  ETIQUETAS_CATEGORIA,
} from '../../../../modelos';

@Component({
  selector: 'app-boda-detalle',
  standalone: true,
  imports: [RouterLink, DatePipe, DecimalPipe, ReactiveFormsModule],
  templateUrl: './boda-detalle.html',
  styleUrl: './boda-detalle.scss',
})
export class BodaDetalle implements OnInit {
  private route = inject(ActivatedRoute);
  private router = inject(Router);
  private adminService = inject(AdminService);

  protected boda = signal<Boda | null>(null);
  protected cargando = signal(true);
  protected cambiandoEstado = signal(false);
  protected guardandoPresupuesto = signal(false);
  protected error = signal('');
  protected exito = signal('');

  protected presupuestoCtrl = new FormControl<number | null>(null, [Validators.required, Validators.min(1)]);

  protected readonly etiquetasEstado = ETIQUETAS_ESTADO;
  protected readonly etiquetasCeremonia = ETIQUETAS_CEREMONIA;
  protected readonly etiquetasFranja = ETIQUETAS_FRANJA;
  protected readonly etiquetasTematica = ETIQUETAS_TEMATICA;
  protected readonly etiquetasComida = ETIQUETAS_COMIDA;
  protected readonly etiquetasPresupuesto = ETIQUETAS_PRESUPUESTO;
  protected readonly etiquetasCategoria = ETIQUETAS_CATEGORIA;

  protected readonly transicionesEstado: Record<EstadoBoda, EstadoBoda[]> = {
    pendiente_reunion: ['activa', 'cancelada'],
    activa: ['finalizada', 'cancelada'],
    finalizada: [],
    cancelada: [],
  };

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.adminService.boda(id).subscribe({
      next: (r) => {
        this.boda.set(r.data.boda);
        if (r.data.boda?.presupuesto_definitivo) {
          this.presupuestoCtrl.setValue(parseFloat(r.data.boda.presupuesto_definitivo));
        }
        this.cargando.set(false);
      },
      error: () => { this.cargando.set(false); this.error.set('Error al cargar la boda.'); },
    });
  }

  protected cambiarEstado(nuevoEstado: EstadoBoda): void {
    const b = this.boda();
    if (!b || this.cambiandoEstado()) return;
    const etiqueta = this.etiquetasEstado[nuevoEstado];
    if (!confirm(`¿Cambiar el estado de la boda a "${etiqueta}"?`)) return;
    this.cambiandoEstado.set(true);
    this.adminService.cambiarEstadoBoda(b.id, nuevoEstado).subscribe({
      next: (r) => {
        this.boda.set(r.data.boda);
        this.exito.set(`Estado cambiado a "${etiqueta}" correctamente.`);
        this.cambiandoEstado.set(false);
        setTimeout(() => this.exito.set(''), 4000);
      },
      error: (e) => {
        this.error.set(e?.error?.message ?? 'Error al cambiar el estado.');
        this.cambiandoEstado.set(false);
      },
    });
  }

  protected fijarPresupuesto(): void {
    const b = this.boda();
    const valor = this.presupuestoCtrl.value;
    if (!b || !valor || this.presupuestoCtrl.invalid || this.guardandoPresupuesto()) return;
    this.guardandoPresupuesto.set(true);
    this.adminService.fijarPresupuestoDefinitivo(b.id, valor).subscribe({
      next: (r) => {
        this.boda.set(r.data.boda);
        this.exito.set('Presupuesto definitivo guardado.');
        this.guardandoPresupuesto.set(false);
        setTimeout(() => this.exito.set(''), 4000);
      },
      error: (e) => {
        this.error.set(e?.error?.message ?? 'Error al fijar el presupuesto.');
        this.guardandoPresupuesto.set(false);
      },
    });
  }
}
