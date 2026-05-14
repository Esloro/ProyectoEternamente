import { Component, OnInit, inject, signal } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';

import { AutenticacionService } from '../../servicios/autenticacion.service';

/**
 * Pagina final tras pulsar el enlace del email de confirmacion. Si la
 * eliminacion fue correcta limpiamos cualquier sesion local restante.
 * Si el backend devuelve ?error=boda_bloqueante significa que el estado
 * de la boda cambio entre la solicitud y la confirmacion.
 */
@Component({
  selector: 'app-cuenta-eliminada',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './cuenta-eliminada.html',
  styleUrl: './cuenta-eliminada.scss',
})
export class CuentaEliminada implements OnInit {
  private ruta = inject(ActivatedRoute);
  private auth = inject(AutenticacionService);

  protected eliminada = signal(false);
  protected motivoError = signal<string | null>(null);

  ngOnInit(): void {
    const params = this.ruta.snapshot.queryParamMap;
    const ok = params.get('ok') === '1';
    const error = params.get('error');

    this.eliminada.set(ok);
    this.motivoError.set(error);

    if (ok) {
      // Si la sesion local seguia abierta en otra pestaña, la limpiamos
      // (sin redirigir, queremos mostrar el mensaje en esta misma pagina).
      this.auth.limpiarSesionLocal();
    }
  }
}
