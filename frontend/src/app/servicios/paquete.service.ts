import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import { Paquete, RespuestaApi } from '../modelos';

@Injectable({ providedIn: 'root' })
export class PaqueteService {
  private http = inject(HttpClient);

  index(): Observable<RespuestaApi<{ paquetes: Paquete[] }>> {
    return this.http.get<RespuestaApi<{ paquetes: Paquete[] }>>(`${environment.apiUrl}/paquetes`);
  }
}
