import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import { Portfolio, RespuestaApi } from '../modelos';

@Injectable({ providedIn: 'root' })
export class PortfolioService {
  private http = inject(HttpClient);

  index(): Observable<RespuestaApi<{ portfolio: Portfolio[] }>> {
    return this.http.get<RespuestaApi<{ portfolio: Portfolio[] }>>(`${environment.apiUrl}/portfolio`);
  }
}
