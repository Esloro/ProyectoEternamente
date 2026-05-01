import { HttpClient } from '@angular/common/http';
import { Injectable, computed, inject, signal } from '@angular/core';
import { Router } from '@angular/router';
import { Observable, tap } from 'rxjs';
import { environment } from '../../environments/environment';
import { Boda, DatosLogin, DatosRegistro, RespuestaApi, Usuario } from '../modelos';

interface RespuestaLogin {
  usuario: Usuario;
  token: string;
}

interface RespuestaYo {
  usuario: Usuario;
  boda: Boda | null;
}

const CLAVE_TOKEN = 'wedding_token';
const CLAVE_USUARIO = 'wedding_usuario';

@Injectable({ providedIn: 'root' })
export class AutenticacionService {
  private http = inject(HttpClient);
  private router = inject(Router);

  // Estado reactivo del usuario logueado.
  private _usuario = signal<Usuario | null>(this.cargarUsuarioPersistido());
  readonly usuario = this._usuario.asReadonly();

  // Helpers derivados.
  readonly estaLogueado = computed(() => this._usuario() !== null);
  readonly esAdministrador = computed(() => this._usuario()?.rol === 'administrador');
  readonly esCliente = computed(() => this._usuario()?.rol === 'cliente');
  readonly emailVerificado = computed(() => this._usuario()?.email_verificado_en !== null);

  registro(datos: DatosRegistro): Observable<RespuestaApi<RespuestaLogin>> {
    return this.http
      .post<RespuestaApi<RespuestaLogin>>(`${environment.apiUrl}/auth/registro`, datos)
      .pipe(tap((r) => this.persistir(r.data.usuario, r.data.token)));
  }

  login(datos: DatosLogin): Observable<RespuestaApi<RespuestaLogin>> {
    return this.http
      .post<RespuestaApi<RespuestaLogin>>(`${environment.apiUrl}/auth/login`, datos)
      .pipe(tap((r) => this.persistir(r.data.usuario, r.data.token)));
  }

  logout(): Observable<RespuestaApi<null>> {
    return this.http
      .post<RespuestaApi<null>>(`${environment.apiUrl}/auth/logout`, {})
      .pipe(tap(() => this.limpiarSesion()));
  }

  yo(): Observable<RespuestaApi<RespuestaYo>> {
    return this.http
      .get<RespuestaApi<RespuestaYo>>(`${environment.apiUrl}/auth/yo`)
      .pipe(tap((r) => this._usuario.set(r.data.usuario)));
  }

  reenviarVerificacion(): Observable<RespuestaApi<null>> {
    return this.http.post<RespuestaApi<null>>(`${environment.apiUrl}/auth/reenviar-verificacion`, {});
  }

  solicitarRecuperacion(email: string): Observable<RespuestaApi<null>> {
    return this.http.post<RespuestaApi<null>>(`${environment.apiUrl}/auth/recuperar-password`, { email });
  }

  resetearPassword(token: string, email: string, password: string, password_confirmation: string): Observable<RespuestaApi<null>> {
    return this.http.post<RespuestaApi<null>>(`${environment.apiUrl}/auth/reset-password`, {
      token,
      email,
      password,
      password_confirmation,
    });
  }

  /**
   * Cierre de sesion por la fuerza (sin llamar al backend).
   * Lo usa el interceptor cuando recibe un 401.
   */
  forzarCierreLocal(): void {
    this.limpiarSesion();
    this.router.navigate(['/login']);
  }

  obtenerToken(): string | null {
    return localStorage.getItem(CLAVE_TOKEN);
  }

  // -----------------------------------------------------------------
  // Persistencia en localStorage
  // -----------------------------------------------------------------

  private persistir(usuario: Usuario, token: string): void {
    localStorage.setItem(CLAVE_TOKEN, token);
    localStorage.setItem(CLAVE_USUARIO, JSON.stringify(usuario));
    this._usuario.set(usuario);
  }

  private limpiarSesion(): void {
    localStorage.removeItem(CLAVE_TOKEN);
    localStorage.removeItem(CLAVE_USUARIO);
    this._usuario.set(null);
  }

  private cargarUsuarioPersistido(): Usuario | null {
    if (typeof localStorage === 'undefined') return null;
    const raw = localStorage.getItem(CLAVE_USUARIO);
    if (!raw) return null;
    try {
      return JSON.parse(raw) as Usuario;
    } catch {
      return null;
    }
  }
}
