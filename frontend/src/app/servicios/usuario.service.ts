import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable, tap } from 'rxjs';
import { environment } from '../../environments/environment';
import { RespuestaApi, Usuario } from '../modelos';
import { AutenticacionService } from './autenticacion.service';

interface DatosActualizarPerfil {
  nombre: string;
  apellidos: string;
  telefono?: string | null;
}

interface DatosCambiarPassword {
  password_actual: string;
  password: string;
  password_confirmation: string;
}

@Injectable({ providedIn: 'root' })
export class UsuarioService {
  private http = inject(HttpClient);
  private auth = inject(AutenticacionService);
  private base = `${environment.apiUrl}/mi-perfil`;

  actualizarPerfil(datos: DatosActualizarPerfil): Observable<RespuestaApi<{ usuario: Usuario }>> {
    return this.http
      .put<RespuestaApi<{ usuario: Usuario }>>(this.base, datos)
      .pipe(tap((r) => this.auth.yo().subscribe()));
  }

  cambiarPassword(datos: DatosCambiarPassword): Observable<RespuestaApi<null>> {
    return this.http.put<RespuestaApi<null>>(`${this.base}/password`, datos);
  }
}
