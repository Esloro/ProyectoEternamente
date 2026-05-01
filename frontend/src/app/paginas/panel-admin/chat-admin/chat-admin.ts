import { Component, OnInit, inject, signal } from '@angular/core';
import { DatePipe } from '@angular/common';
import { RouterLink } from '@angular/router';

import { AdminService } from '../../../servicios/admin.service';
import { Boda, Mensaje, Usuario } from '../../../modelos';

interface ConversacionResumen {
  boda: Pick<Boda, 'id' | 'estado' | 'fecha_boda'>;
  cliente: Usuario;
  ultimo: Mensaje | null;
  no_leidos: number;
}

@Component({
  selector: 'app-chat-admin',
  standalone: true,
  imports: [RouterLink, DatePipe],
  templateUrl: './chat-admin.html',
  styleUrl: './chat-admin.scss',
})
export class ChatAdmin implements OnInit {
  private adminService = inject(AdminService);

  protected conversaciones = signal<ConversacionResumen[]>([]);
  protected cargando = signal(true);

  ngOnInit(): void {
    this.adminService.conversaciones().subscribe({
      next: (r) => {
        this.conversaciones.set(r.data.conversaciones ?? []);
        this.cargando.set(false);
      },
      error: () => this.cargando.set(false),
    });
  }
}
