import { Component, OnInit, inject, signal } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { RouterLink } from '@angular/router';

import { BodaService } from '../../../servicios/boda.service';
import { Boda, ETIQUETAS_CATEGORIA, Proveedor } from '../../../modelos';

@Component({
  selector: 'app-presupuesto',
  standalone: true,
  imports: [DecimalPipe, RouterLink],
  templateUrl: './presupuesto.html',
  styleUrl: './presupuesto.scss',
})
export class Presupuesto implements OnInit {
  private bodaService = inject(BodaService);

  protected boda = signal<Boda | null>(null);
  protected proveedores = signal<Proveedor[]>([]);
  protected cargando = signal(true);
  protected solicitando = signal(false);
  protected solicitudEnviada = signal(false);
  protected error = signal('');

  protected readonly etiquetasCategoria = ETIQUETAS_CATEGORIA;

  ngOnInit(): void {
    Promise.all([
      this.bodaService.miBoda().toPromise(),
      this.bodaService.misProveedores().toPromise(),
    ]).then(([bodaRes, provRes]) => {
      this.boda.set(bodaRes?.data.boda ?? null);
      this.proveedores.set(provRes?.data.proveedores ?? []);
      this.cargando.set(false);
    }).catch(() => this.cargando.set(false));
  }

  protected get totalProveedores(): number {
    return this.proveedores().reduce((s, p) => s + parseFloat(p.precio), 0);
  }

  protected solicitarPresupuesto(): void {
    if (this.solicitando() || this.solicitudEnviada()) return;
    if (!confirm('¿Confirmas que quieres solicitar el presupuesto definitivo al equipo de WeddingPlanner?')) return;
    this.solicitando.set(true);
    this.bodaService.solicitarPresupuestoDefinitivo().subscribe({
      next: () => {
        this.solicitudEnviada.set(true);
        this.solicitando.set(false);
      },
      error: (e) => {
        this.error.set(e?.error?.message ?? 'Error al solicitar presupuesto.');
        this.solicitando.set(false);
      },
    });
  }
}
