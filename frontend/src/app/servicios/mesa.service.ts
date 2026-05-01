import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import { Mesa, RespuestaApi } from '../modelos';

export interface DatosMesa {
  numero: number;
  capacidad: number;
}

@Injectable({ providedIn: 'root' })
export class MesaService {
  private http = inject(HttpClient);
  private base = `${environment.apiUrl}/mi-boda/mesas`;

  index(): Observable<RespuestaApi<{ mesas: Mesa[] }>> {
    return this.http.get<RespuestaApi<{ mesas: Mesa[] }>>(this.base);
  }

  crear(datos: DatosMesa): Observable<RespuestaApi<{ mesa: Mesa }>> {
    return this.http.post<RespuestaApi<{ mesa: Mesa }>>(this.base, datos);
  }

  actualizar(id: number, datos: DatosMesa): Observable<RespuestaApi<{ mesa: Mesa }>> {
    return this.http.put<RespuestaApi<{ mesa: Mesa }>>(`${this.base}/${id}`, datos);
  }

  eliminar(id: number): Observable<RespuestaApi<null>> {
    return this.http.delete<RespuestaApi<null>>(`${this.base}/${id}`);
  }
}
