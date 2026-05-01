import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import { RespuestaApi, Testimonio } from '../modelos';

@Injectable({ providedIn: 'root' })
export class TestimonioService {
  private http = inject(HttpClient);

  index(): Observable<RespuestaApi<{ testimonios: Testimonio[] }>> {
    return this.http.get<RespuestaApi<{ testimonios: Testimonio[] }>>(`${environment.apiUrl}/testimonios`);
  }
}
