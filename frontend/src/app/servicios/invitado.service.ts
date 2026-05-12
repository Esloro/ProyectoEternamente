import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import { Invitado, RespuestaApi } from '../modelos';

export interface DatosInvitado {
  nombre: string;
  alergias?: string | null;
  num_acompanantes?: number;
  mesa_id?: number | null;
}

@Injectable({ providedIn: 'root' })
export class InvitadoService {
  private http = inject(HttpClient);
  private base = `${environment.apiUrl}/mi-boda/invitados`;

  index(): Observable<RespuestaApi<{ invitados: Invitado[] }>> {
    return this.http.get<RespuestaApi<{ invitados: Invitado[] }>>(this.base);
  }

  crear(datos: DatosInvitado): Observable<RespuestaApi<{ invitado: Invitado }>> {
    return this.http.post<RespuestaApi<{ invitado: Invitado }>>(this.base, datos);
  }

  actualizar(id: number, datos: DatosInvitado): Observable<RespuestaApi<{ invitado: Invitado }>> {
    return this.http.put<RespuestaApi<{ invitado: Invitado }>>(`${this.base}/${id}`, datos);
  }

  eliminar(id: number): Observable<RespuestaApi<null>> {
    return this.http.delete<RespuestaApi<null>>(`${this.base}/${id}`);
  }

  /**
   * Asigna (o desasigna) un invitado a una mesa. Pensado para drag&drop.
   */
  asignarMesa(invitadoId: number, mesaId: number | null): Observable<RespuestaApi<{ invitado: Invitado }>> {
    return this.http.post<RespuestaApi<{ invitado: Invitado }>>(`${environment.apiUrl}/mi-boda/asignar-mesa`, {
      invitado_id: invitadoId,
      mesa_id: mesaId,
    });
  }
}
