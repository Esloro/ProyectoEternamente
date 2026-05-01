import {
  AfterViewChecked,
  Component,
  ElementRef,
  OnDestroy,
  OnInit,
  ViewChild,
  inject,
  signal,
} from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { DatePipe } from '@angular/common';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { Subscription, interval, switchMap } from 'rxjs';

import { AdminService } from '../../../../servicios/admin.service';
import { AutenticacionService } from '../../../../servicios/autenticacion.service';
import { Boda, Mensaje } from '../../../../modelos';
import { environment } from '../../../../../environments/environment';

@Component({
  selector: 'app-chat-admin-boda',
  standalone: true,
  imports: [RouterLink, DatePipe, ReactiveFormsModule],
  templateUrl: './chat-admin-boda.html',
  styleUrl: './chat-admin-boda.scss',
})
export class ChatAdminBoda implements OnInit, OnDestroy, AfterViewChecked {
  @ViewChild('contenedorMensajes') private contenedor!: ElementRef<HTMLDivElement>;

  private route = inject(ActivatedRoute);
  private adminService = inject(AdminService);
  protected auth = inject(AutenticacionService);

  protected bodaId = signal(0);
  protected boda = signal<Boda | null>(null);
  protected mensajes = signal<Mensaje[]>([]);
  protected cargando = signal(true);
  protected enviando = signal(false);
  protected texto = new FormControl('');

  private pollingSub?: Subscription;
  private debeHacerScroll = false;

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('bodaId'));
    this.bodaId.set(id);
    this.cargar();

    this.pollingSub = interval(environment.intervaloPollingChat).pipe(
      switchMap(() => this.adminService.conversacionConBoda(id)),
    ).subscribe({
      next: (r) => {
        this.mensajes.set(r.data.mensajes);
        this.debeHacerScroll = true;
        this.adminService.marcarLeidosBoda(id).subscribe();
      },
    });
  }

  ngOnDestroy(): void {
    this.pollingSub?.unsubscribe();
  }

  ngAfterViewChecked(): void {
    if (this.debeHacerScroll) {
      this.scrollAbajo();
      this.debeHacerScroll = false;
    }
  }

  private cargar(): void {
    const id = this.bodaId();
    this.adminService.conversacionConBoda(id).subscribe({
      next: (r) => {
        this.boda.set(r.data.boda);
        this.mensajes.set(r.data.mensajes);
        this.cargando.set(false);
        this.debeHacerScroll = true;
        this.adminService.marcarLeidosBoda(id).subscribe();
      },
      error: () => this.cargando.set(false),
    });
  }

  private scrollAbajo(): void {
    try {
      const el = this.contenedor?.nativeElement;
      if (el) el.scrollTop = el.scrollHeight;
    } catch {}
  }

  protected enviar(): void {
    const contenido = (this.texto.value ?? '').trim();
    if (!contenido || this.enviando()) return;
    this.enviando.set(true);
    this.adminService.enviarMensajeABoda(this.bodaId(), contenido).subscribe({
      next: (r) => {
        this.mensajes.update((msgs) => [...msgs, r.data.mensaje]);
        this.texto.setValue('');
        this.enviando.set(false);
        this.debeHacerScroll = true;
      },
      error: () => this.enviando.set(false),
    });
  }

  protected onKeydown(event: KeyboardEvent): void {
    if (event.key === 'Enter' && !event.shiftKey) {
      event.preventDefault();
      this.enviar();
    }
  }

  protected esMio(msg: Mensaje): boolean {
    return msg.emisor_id === this.auth.usuario()?.id;
  }
}
