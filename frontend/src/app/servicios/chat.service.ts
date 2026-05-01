import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable, interval, switchMap } from 'rxjs';
import { environment } from '../../environments/environment';
import { Mensaje, RespuestaApi } from '../modelos';

interface RespuestaConversacion {
  mensajes: Mensaje[];
  no_leidos: number;
}

@Injectable({ providedIn: 'root' })
export class ChatService {
  private http = inject(HttpClient);
  private base = `${environment.apiUrl}/mi-boda/chat`;

  conversacion(): Observable<RespuestaApi<RespuestaConversacion>> {
    return this.http.get<RespuestaApi<RespuestaConversacion>>(this.base);
  }

  /**
   * Stream de la conversacion via polling cada N ms (definido en environment).
   * Hay que recordar des-suscribirse al destruir el componente.
   */
  conversacionStream(): Observable<RespuestaApi<RespuestaConversacion>> {
    return interval(environment.intervaloPollingChat).pipe(
      switchMap(() => this.conversacion()),
    );
  }

  enviar(contenido: string): Observable<RespuestaApi<{ mensaje: Mensaje }>> {
    return this.http.post<RespuestaApi<{ mensaje: Mensaje }>>(this.base, { contenido });
  }

  marcarLeidos(): Observable<RespuestaApi<{ marcados_leidos: number }>> {
    return this.http.post<RespuestaApi<{ marcados_leidos: number }>>(`${this.base}/leer`, {});
  }
}
