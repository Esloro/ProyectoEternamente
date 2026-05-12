import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { Router, RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';

import { AutenticacionService } from '../../servicios/autenticacion.service';
import { BodaService } from '../../servicios/boda.service';
import { ETIQUETAS_ESTADO } from '../../modelos';

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

  protected boda = this.bodaService.bodaActual;
  protected menuMovilAbierto = signal(false);
  protected reenvioEnviado = signal(false);

  protected cuentaAtras = computed<number | null>(() => {
    const fecha = this.boda()?.fecha_boda;
    if (!fecha) return null;
    const objetivo = new Date(fecha);
    if (Number.isNaN(objetivo.getTime())) return null;
    objetivo.setHours(0, 0, 0, 0);
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    const dias = Math.round((objetivo.getTime() - hoy.getTime()) / 86_400_000);
    return Math.max(0, dias);
  });

  protected bodaActiva = computed(() => this.boda()?.estado === 'activa');
  protected emailSinVerificar = computed(() => !this.auth.emailVerificado());
  protected etiquetaEstado = computed(() => {
    const b = this.boda();
    return b ? ETIQUETAS_ESTADO[b.estado] : null;
  });

  ngOnInit(): void {
    this.bodaService.miBoda().subscribe();
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
