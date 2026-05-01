import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { Router, RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';

import { AutenticacionService } from '../../servicios/autenticacion.service';
import { BodaService } from '../../servicios/boda.service';
import { Boda, ETIQUETAS_ESTADO } from '../../modelos';

@Component({
  selector: 'app-panel-cliente',
  standalone: true,
  imports: [RouterOutlet, RouterLink, RouterLinkActive],
  templateUrl: './panel-cliente.html',
  styleUrl: './panel-cliente.scss',
})
export class PanelCliente implements OnInit {
  protected auth = inject(AutenticacionService);
  private bodaService = inject(BodaService);
  private router = inject(Router);

  protected boda = signal<Boda | null>(null);
  protected cuentaAtras = signal<number | null>(null);
  protected menuMovilAbierto = signal(false);
  protected reenvioEnviado = signal(false);

  protected bodaActiva = computed(() => this.boda()?.estado === 'activa');
  protected emailSinVerificar = computed(() => !this.auth.emailVerificado());
  protected etiquetaEstado = computed(() => {
    const b = this.boda();
    return b ? ETIQUETAS_ESTADO[b.estado] : null;
  });

  ngOnInit(): void {
    this.bodaService.miBoda().subscribe({
      next: (r) => {
        this.boda.set(r.data.boda);
        this.cuentaAtras.set(r.data.cuenta_atras_dias ?? null);
      },
    });
  }

  protected reenviarVerificacion(): void {
    this.auth.reenviarVerificacion().subscribe({
      next: () => this.reenvioEnviado.set(true),
    });
  }

  protected cerrarSesion(): void {
    this.auth.logout().subscribe({
      next: () => this.router.navigate(['/login']),
      error: () => this.router.navigate(['/login']),
    });
  }

  protected toggleMenuMovil(): void {
    this.menuMovilAbierto.update((v) => !v);
  }

  protected cerrarMenuMovil(): void {
    this.menuMovilAbierto.set(false);
  }
}
