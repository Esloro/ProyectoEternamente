import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import {
  Boda,
  EstadoBoda,
  Mensaje,
  MensajeContacto,
  Paquete,
  Portfolio,
  Proveedor,
  RespuestaApi,
  RespuestaPaginada,
  Testimonio,
  Usuario,
} from '../modelos';

interface ResumenDashboard {
  bodas: {
    pendiente_reunion: number;
    activas: number;
    finalizadas: number;
    canceladas: number;
  };
  clientes_total: number;
  mensajes_no_leidos: number;
  chats_no_leidos: number;
  ingresos_estimados: number;
  proximas_bodas: Boda[];
}

interface ConversacionResumen {
  boda: Pick<Boda, 'id' | 'estado' | 'fecha_boda'>;
  cliente: Usuario;
  ultimo: Mensaje | null;
  no_leidos: number;
}

@Injectable({ providedIn: 'root' })
export class AdminService {
  private http = inject(HttpClient);
  private base = `${environment.apiUrl}/admin`;

  // ----- Dashboard -----
  dashboard(): Observable<RespuestaApi<ResumenDashboard>> {
    return this.http.get<RespuestaApi<ResumenDashboard>>(`${this.base}/dashboard`);
  }

  // ----- Clientes -----
  clientes(busqueda?: string): Observable<RespuestaApi<RespuestaPaginada<Usuario>>> {
    let params = new HttpParams();
    if (busqueda) params = params.set('q', busqueda);
    return this.http.get<RespuestaApi<RespuestaPaginada<Usuario>>>(`${this.base}/clientes`, { params });
  }

  cliente(id: number): Observable<RespuestaApi<{ cliente: Usuario }>> {
    return this.http.get<RespuestaApi<{ cliente: Usuario }>>(`${this.base}/clientes/${id}`);
  }

  verificarEmailCliente(id: number): Observable<RespuestaApi<{ cliente: Usuario }>> {
    return this.http.post<RespuestaApi<{ cliente: Usuario }>>(`${this.base}/clientes/${id}/verificar-email`, {});
  }

  // ----- Bodas -----
  bodas(filtros: { estado?: EstadoBoda; q?: string } = {}): Observable<RespuestaApi<RespuestaPaginada<Boda>>> {
    let params = new HttpParams();
    if (filtros.estado) params = params.set('estado', filtros.estado);
    if (filtros.q) params = params.set('q', filtros.q);
    return this.http.get<RespuestaApi<RespuestaPaginada<Boda>>>(`${this.base}/bodas`, { params });
  }

  boda(id: number): Observable<RespuestaApi<{ boda: Boda }>> {
    return this.http.get<RespuestaApi<{ boda: Boda }>>(`${this.base}/bodas/${id}`);
  }

  cambiarEstadoBoda(id: number, estado: EstadoBoda): Observable<RespuestaApi<{ boda: Boda }>> {
    return this.http.put<RespuestaApi<{ boda: Boda }>>(`${this.base}/bodas/${id}/estado`, { estado });
  }

  fijarPresupuestoDefinitivo(id: number, presupuesto: number): Observable<RespuestaApi<{ boda: Boda }>> {
    return this.http.put<RespuestaApi<{ boda: Boda }>>(`${this.base}/bodas/${id}/presupuesto`, {
      presupuesto_definitivo: presupuesto,
    });
  }

  // ----- Proveedores (CRUD) -----
  listarProveedores(filtros: { categoria?: string; q?: string } = {}): Observable<RespuestaApi<RespuestaPaginada<Proveedor>>> {
    let params = new HttpParams();
    if (filtros.categoria) params = params.set('categoria', filtros.categoria);
    if (filtros.q) params = params.set('q', filtros.q);
    return this.http.get<RespuestaApi<RespuestaPaginada<Proveedor>>>(`${this.base}/proveedores`, { params });
  }
  crearProveedor(datos: Partial<Proveedor>) {
    return this.http.post<RespuestaApi<{ proveedor: Proveedor }>>(`${this.base}/proveedores`, datos);
  }
  actualizarProveedor(id: number, datos: Partial<Proveedor>) {
    return this.http.put<RespuestaApi<{ proveedor: Proveedor }>>(`${this.base}/proveedores/${id}`, datos);
  }
  eliminarProveedor(id: number) {
    return this.http.delete<RespuestaApi<null>>(`${this.base}/proveedores/${id}`);
  }

  // ----- Paquetes (CRUD) -----
  listarPaquetes() {
    return this.http.get<RespuestaApi<{ paquetes: Paquete[] }>>(`${this.base}/paquetes`);
  }
  crearPaquete(datos: Partial<Paquete>) {
    return this.http.post<RespuestaApi<{ paquete: Paquete }>>(`${this.base}/paquetes`, datos);
  }
  actualizarPaquete(id: number, datos: Partial<Paquete>) {
    return this.http.put<RespuestaApi<{ paquete: Paquete }>>(`${this.base}/paquetes/${id}`, datos);
  }
  eliminarPaquete(id: number) {
    return this.http.delete<RespuestaApi<null>>(`${this.base}/paquetes/${id}`);
  }

  // ----- Portfolio (CRUD) -----
  listarPortfolio() {
    return this.http.get<RespuestaApi<{ portfolio: Portfolio[] }>>(`${this.base}/portfolio`);
  }
  crearPortfolio(datos: Partial<Portfolio>) {
    return this.http.post<RespuestaApi<{ portfolio: Portfolio }>>(`${this.base}/portfolio`, datos);
  }
  actualizarPortfolio(id: number, datos: Partial<Portfolio>) {
    return this.http.put<RespuestaApi<{ portfolio: Portfolio }>>(`${this.base}/portfolio/${id}`, datos);
  }
  eliminarPortfolio(id: number) {
    return this.http.delete<RespuestaApi<null>>(`${this.base}/portfolio/${id}`);
  }

  // ----- Testimonios (CRUD) -----
  listarTestimonios() {
    return this.http.get<RespuestaApi<{ testimonios: Testimonio[] }>>(`${this.base}/testimonios`);
  }
  crearTestimonio(datos: Partial<Testimonio>) {
    return this.http.post<RespuestaApi<{ testimonio: Testimonio }>>(`${this.base}/testimonios`, datos);
  }
  actualizarTestimonio(id: number, datos: Partial<Testimonio>) {
    return this.http.put<RespuestaApi<{ testimonio: Testimonio }>>(`${this.base}/testimonios/${id}`, datos);
  }
  eliminarTestimonio(id: number) {
    return this.http.delete<RespuestaApi<null>>(`${this.base}/testimonios/${id}`);
  }

  // ----- Mensajes de contacto -----
  listarMensajesContacto(soloNoLeidos = false): Observable<RespuestaApi<RespuestaPaginada<MensajeContacto>>> {
    let params = new HttpParams();
    if (soloNoLeidos) params = params.set('solo_no_leidos', '1');
    return this.http.get<RespuestaApi<RespuestaPaginada<MensajeContacto>>>(`${this.base}/mensajes-contacto`, { params });
  }
  marcarMensajeContactoLeido(id: number) {
    return this.http.post<RespuestaApi<{ mensaje: MensajeContacto }>>(`${this.base}/mensajes-contacto/${id}/leer`, {});
  }
  eliminarMensajeContacto(id: number) {
    return this.http.delete<RespuestaApi<null>>(`${this.base}/mensajes-contacto/${id}`);
  }

  // ----- Chat (admin) -----
  conversaciones(): Observable<RespuestaApi<{ conversaciones: ConversacionResumen[] }>> {
    return this.http.get<RespuestaApi<{ conversaciones: ConversacionResumen[] }>>(`${this.base}/chat`);
  }
  conversacionConBoda(bodaId: number) {
    return this.http.get<RespuestaApi<{ boda: Boda; mensajes: Mensaje[] }>>(`${this.base}/chat/${bodaId}`);
  }
  enviarMensajeABoda(bodaId: number, contenido: string) {
    return this.http.post<RespuestaApi<{ mensaje: Mensaje }>>(`${this.base}/chat/${bodaId}`, { contenido });
  }
  marcarLeidosBoda(bodaId: number) {
    return this.http.post<RespuestaApi<{ marcados_leidos: number }>>(`${this.base}/chat/${bodaId}/leer`, {});
  }
}
