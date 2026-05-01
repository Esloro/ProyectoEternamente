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
}
