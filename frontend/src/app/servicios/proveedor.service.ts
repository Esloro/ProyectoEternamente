import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import { CategoriaConProveedores, CategoriaProveedor, Proveedor, RespuestaApi } from '../modelos';

@Injectable({ providedIn: 'root' })
export class ProveedorService {
  private http = inject(HttpClient);
  private base = `${environment.apiUrl}/proveedores`;

  /**
   * Devuelve todos los proveedores agrupados por categoria.
   */
  todos(): Observable<RespuestaApi<{ categorias: CategoriaConProveedores[] }>> {
    return this.http.get<RespuestaApi<{ categorias: CategoriaConProveedores[] }>>(this.base);
  }

  porCategoria(categoria: CategoriaProveedor): Observable<RespuestaApi<{ categoria: string; etiqueta: string; proveedores: Proveedor[] }>> {
    return this.http.get<RespuestaApi<{ categoria: string; etiqueta: string; proveedores: Proveedor[] }>>(`${this.base}/${categoria}`);
  }
}
