import { Component, OnInit, inject, signal } from '@angular/core';
import { DatePipe } from '@angular/common';

import { AdminService } from '../../../servicios/admin.service';
import { Usuario } from '../../../modelos';

@Component({
  selector: 'app-clientes',
  standalone: true,
  imports: [DatePipe],
  templateUrl: './clientes.html',
  styleUrl: './clientes.scss',
})
export class Clientes implements OnInit {
  private adminService = inject(AdminService);

  protected clientes = signal<Usuario[]>([]);
  protected cargando = signal(true);
  protected clienteAEliminar = signal<Usuario | null>(null);
  protected eliminando = signal(false);
  protected errorEliminacion = signal('');

  ngOnInit(): void {
    this.cargar();
  }

  private cargar(q?: string): void {
    this.cargando.set(true);
    this.adminService.clientes(q).subscribe({
      next: (r) => { this.clientes.set(r.data.data ?? []); this.cargando.set(false); },
      error: () => this.cargando.set(false),
    });
  }

  protected onBusqueda(event: Event): void {
    this.cargar((event.target as HTMLInputElement).value);
  }

  protected verificarEmail(usuario: Usuario): void {
    this.adminService.verificarEmailCliente(usuario.id).subscribe({
      next: (r) => {
        this.clientes.update(lista =>
          lista.map(c => c.id === usuario.id ? { ...c, email_verificado_en: r.data.cliente.email_verificado_en } : c)
        );
      },
    });
  }

  protected abrirConfirmacionEliminar(usuario: Usuario): void {
    this.errorEliminacion.set('');
    this.clienteAEliminar.set(usuario);
  }

  protected cancelarEliminar(): void {
    if (this.eliminando()) return;
    this.clienteAEliminar.set(null);
  }

  protected confirmarEliminar(): void {
    const cliente = this.clienteAEliminar();
    if (!cliente || this.eliminando()) return;

    this.eliminando.set(true);
    this.errorEliminacion.set('');

    this.adminService.eliminarCliente(cliente.id).subscribe({
      next: () => {
        this.clientes.update(lista => lista.filter(c => c.id !== cliente.id));
        this.eliminando.set(false);
        this.clienteAEliminar.set(null);
      },
      error: (e) => {
        this.eliminando.set(false);
        this.errorEliminacion.set(e?.error?.message ?? 'No se ha podido eliminar el cliente.');
      },
    });
  }
}
