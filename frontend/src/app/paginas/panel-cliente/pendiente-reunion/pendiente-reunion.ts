import { Component, OnInit, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';

import { BodaService } from '../../../servicios/boda.service';
import { Boda, ETIQUETAS_CEREMONIA, ETIQUETAS_FRANJA, ETIQUETAS_TEMATICA } from '../../../modelos';

@Component({
  selector: 'app-pendiente-reunion',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './pendiente-reunion.html',
  styleUrl: './pendiente-reunion.scss',
})
export class PendienteReunion implements OnInit {
  private bodaService = inject(BodaService);

  protected boda = signal<Boda | null>(null);
  protected cargando = signal(true);

  protected readonly etiquetasCeremonia = ETIQUETAS_CEREMONIA;
  protected readonly etiquetasFranja = ETIQUETAS_FRANJA;
  protected readonly etiquetasTematica = ETIQUETAS_TEMATICA;

  ngOnInit(): void {
    this.bodaService.miBoda().subscribe({
      next: (r) => {
        this.boda.set(r.data.boda);
        this.cargando.set(false);
      },
      error: () => this.cargando.set(false),
    });
  }

  protected formatearFecha(fecha: string): string {
    return new Date(fecha).toLocaleDateString('es-ES', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    });
  }
}
