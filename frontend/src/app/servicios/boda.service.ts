import { HttpClient } from '@angular/common/http';
import { Injectable, inject, signal } from '@angular/core';
import { Observable } from 'rxjs';
import { tap } from 'rxjs/operators';
import { environment } from '../../environments/environment';
import { Boda, DatosCuestionarioInicial, Proveedor, RespuestaApi } from '../modelos';

interface RespuestaMiBoda {
  boda: Boda | null;
  cuenta_atras_dias?: number;
}

interface RespuestaMisProveedores {
  proveedores: Proveedor[];
}

interface ProveedorElegido {
  id: number;
  notas?: string | null;
}

@Injectable({ providedIn: 'root' })
export class BodaService {
  private http = inject(HttpClient);
  private base = `${environment.apiUrl}/mi-boda`;

  // Estado compartido de la boda actual del cliente. Cualquier componente
  // que muestre datos de la boda (panel-cliente, resumen-boda, etc.) puede
  // suscribirse a esta senal para reaccionar a cambios sin recargar.
  readonly bodaActual = signal<Boda | null>(null);

  miBoda(): Observable<RespuestaApi<RespuestaMiBoda>> {
    return this.http
      .get<RespuestaApi<RespuestaMiBoda>>(this.base)
      .pipe(tap((r) => this.bodaActual.set(r.data.boda)));
  }

  guardarCuestionario(datos: DatosCuestionarioInicial): Observable<RespuestaApi<{ boda: Boda }>> {
    return this.http
      .post<RespuestaApi<{ boda: Boda }>>(`${this.base}/cuestionario`, datos)
      .pipe(tap((r) => this.bodaActual.set(r.data.boda)));
  }

  actualizarDetalles(datos: DatosCuestionarioInicial): Observable<RespuestaApi<{ boda: Boda }>> {
    return this.http
      .put<RespuestaApi<{ boda: Boda }>>(`${this.base}/detalles`, datos)
      .pipe(tap((r) => this.bodaActual.set(r.data.boda)));
  }

  misProveedores(): Observable<RespuestaApi<RespuestaMisProveedores>> {
    return this.http.get<RespuestaApi<RespuestaMisProveedores>>(`${this.base}/proveedores`);
  }

  guardarProveedores(proveedores: ProveedorElegido[]): Observable<RespuestaApi<{ boda: Boda }>> {
    return this.http.post<RespuestaApi<{ boda: Boda }>>(`${this.base}/proveedores`, { proveedores });
  }

  solicitarPresupuestoDefinitivo(): Observable<RespuestaApi<null>> {
    return this.http.post<RespuestaApi<null>>(`${this.base}/solicitar-presupuesto`, {});
  }
}
