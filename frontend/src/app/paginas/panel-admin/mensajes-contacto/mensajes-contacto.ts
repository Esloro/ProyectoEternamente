import { Component, OnInit, inject, signal } from '@angular/core';
import { DatePipe } from '@angular/common';

import { AdminService } from '../../../servicios/admin.service';
import { MensajeContacto } from '../../../modelos';

@Component({
  selector: 'app-mensajes-contacto-admin',
  standalone: true,
  imports: [DatePipe],
  templateUrl: './mensajes-contacto.html',
  styleUrl: './mensajes-contacto.scss',
})
export class MensajesContactoAdmin implements OnInit {
  private adminService = inject(AdminService);

  protected mensajes = signal<MensajeContacto[]>([]);
  protected cargando = signal(true);
  protected mensajeAbierto = signal<MensajeContacto | null>(null);

  ngOnInit(): void {
    this.cargar();
  }

  private cargar(): void {
    this.adminService.listarMensajesContacto().subscribe({
      next: (r) => { this.mensajes.set(r.data.data ?? []); this.cargando.set(false); },
      error: () => this.cargando.set(false),
    });
  }

  protected abrirMensaje(msg: MensajeContacto): void {
    this.mensajeAbierto.set(msg);
    if (!msg.leido) {
      this.adminService.marcarMensajeContactoLeido(msg.id).subscribe({
        next: () => {
          this.mensajes.update((msgs) => msgs.map((m) => m.id === msg.id ? { ...m, leido: true } : m));
          this.mensajeAbierto.update((m) => m ? { ...m, leido: true } : m);
        },
      });
    }
  }

  protected eliminar(id: number): void {
    if (!confirm('¿Eliminar este mensaje?')) return;
    this.adminService.eliminarMensajeContacto(id).subscribe({
      next: () => {
        this.mensajes.update((msgs) => msgs.filter((m) => m.id !== id));
        if (this.mensajeAbierto()?.id === id) this.mensajeAbierto.set(null);
      },
    });
  }
}
