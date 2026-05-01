import {
  Component,
  OnInit,
  OnDestroy,
  AfterViewChecked,
  ElementRef,
  ViewChild,
  inject,
  signal,
} from '@angular/core';
import { DatePipe } from '@angular/common';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { Subscription } from 'rxjs';

import { ChatService } from '../../../servicios/chat.service';
import { AutenticacionService } from '../../../servicios/autenticacion.service';
import { Mensaje } from '../../../modelos';

@Component({
  selector: 'app-chat-cliente',
  standalone: true,
  imports: [DatePipe, ReactiveFormsModule],
  templateUrl: './chat-cliente.html',
  styleUrl: './chat-cliente.scss',
})
export class ChatCliente implements OnInit, OnDestroy, AfterViewChecked {
  @ViewChild('contenedorMensajes') private contenedor!: ElementRef<HTMLDivElement>;

  private chatService = inject(ChatService);
  protected auth = inject(AutenticacionService);

  protected mensajes = signal<Mensaje[]>([]);
  protected cargando = signal(true);
  protected enviando = signal(false);
  protected texto = new FormControl('');

  private pollingSub?: Subscription;
  private debeHacerScroll = false;

  ngOnInit(): void {
    this.cargarConversacion();
    this.pollingSub = this.chatService.conversacionStream().subscribe({
      next: (r) => {
        this.mensajes.set(r.data.mensajes);
        this.debeHacerScroll = true;
        if (r.data.no_leidos > 0) this.chatService.marcarLeidos().subscribe();
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

  private cargarConversacion(): void {
    this.chatService.conversacion().subscribe({
      next: (r) => {
        this.mensajes.set(r.data.mensajes);
        this.cargando.set(false);
        this.debeHacerScroll = true;
        if (r.data.no_leidos > 0) this.chatService.marcarLeidos().subscribe();
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
    this.chatService.enviar(contenido).subscribe({
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
