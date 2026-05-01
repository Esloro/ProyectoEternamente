import { Component, OnInit, inject, signal } from '@angular/core';
import { DecimalPipe, DatePipe } from '@angular/common';
import { RouterLink } from '@angular/router';

import { BodaService } from '../../../servicios/boda.service';
import {
  Boda,
  ETIQUETAS_CEREMONIA,
  ETIQUETAS_ESTADO,
  ETIQUETAS_FRANJA,
  ETIQUETAS_TEMATICA,
  ETIQUETAS_COMIDA,
  ETIQUETAS_PRESUPUESTO,
} from '../../../modelos';

@Component({
  selector: 'app-resumen-boda',
  standalone: true,
  imports: [RouterLink, DecimalPipe, DatePipe],
  templateUrl: './resumen-boda.html',
  styleUrl: './resumen-boda.scss',
})
export class ResumenBoda implements OnInit {
  private bodaService = inject(BodaService);

  protected boda = signal<Boda | null>(null);
  protected cuentaAtras = signal<number | null>(null);
  protected cargando = signal(true);

  protected readonly etiquetasEstado = ETIQUETAS_ESTADO;
  protected readonly etiquetasCeremonia = ETIQUETAS_CEREMONIA;
  protected readonly etiquetasFranja = ETIQUETAS_FRANJA;
  protected readonly etiquetasTematica = ETIQUETAS_TEMATICA;
  protected readonly etiquetasComida = ETIQUETAS_COMIDA;
  protected readonly etiquetasPresupuesto = ETIQUETAS_PRESUPUESTO;

  ngOnInit(): void {
    this.bodaService.miBoda().subscribe({
      next: (r) => {
        this.boda.set(r.data.boda);
        this.cuentaAtras.set(r.data.cuenta_atras_dias ?? null);
        this.cargando.set(false);
      },
      error: () => this.cargando.set(false),
    });
  }
}
