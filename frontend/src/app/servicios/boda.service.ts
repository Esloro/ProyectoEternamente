import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
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

  miBoda(): Observable<RespuestaApi<RespuestaMiBoda>> {
    return this.http.get<RespuestaApi<RespuestaMiBoda>>(this.base);
  }

  guardarCuestionario(datos: DatosCuestionarioInicial): Observable<RespuestaApi<{ boda: Boda }>> {
    return this.http.post<RespuestaApi<{ boda: Boda }>>(`${this.base}/cuestionario`, datos);
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
