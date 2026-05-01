import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import { DatosFormularioContacto, MensajeContacto, RespuestaApi } from '../modelos';

@Injectable({ providedIn: 'root' })
export class MensajeContactoService {
  private http = inject(HttpClient);

  enviar(datos: DatosFormularioContacto): Observable<RespuestaApi<{ mensaje: MensajeContacto }>> {
    return this.http.post<RespuestaApi<{ mensaje: MensajeContacto }>>(`${environment.apiUrl}/contacto`, datos);
  }
}
