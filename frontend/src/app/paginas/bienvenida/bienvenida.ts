import { Component, inject, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment';
import { Paquete, RespuestaApi } from '../../modelos';

/**
 * Componente placeholder del paso 4. Sirve como "smoke test" del setup
 * Angular: hace una llamada GET /api/paquetes para confirmar que
 * HttpClient + interceptor + CORS + backend funcionan end-to-end.
 *
 * Sera reemplazado por la landing real en el paso 5.
 */
@Component({
  selector: 'app-bienvenida',
  imports: [],
  templateUrl: './bienvenida.html',
  styleUrl: './bienvenida.scss',
})
export class Bienvenida {
  private http = inject(HttpClient);

  cargando = signal(true);
  error = signal<string | null>(null);
  paquetes = signal<Paquete[]>([]);

  constructor() {
    this.http
      .get<RespuestaApi<{ paquetes: Paquete[] }>>(`${environment.apiUrl}/paquetes`)
      .subscribe({
        next: (respuesta) => {
          this.paquetes.set(respuesta.data.paquetes);
          this.cargando.set(false);
        },
        error: (err) => {
          this.error.set('No se pudo conectar con el backend: ' + err.message);
          this.cargando.set(false);
        },
      });
  }
}
