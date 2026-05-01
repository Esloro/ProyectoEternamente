import { Component, OnInit, inject, signal } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';

/**
 * Pagina a la que el backend redirige tras procesar el enlace de
 * verificacion de email. El query param ?ok=1 indica exito, cualquier
 * otra cosa indica que algo fue mal.
 */
@Component({
  selector: 'app-email-verificado',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './email-verificado.html',
  styleUrl: './email-verificado.scss',
})
export class EmailVerificado implements OnInit {
  private ruta = inject(ActivatedRoute);

  protected verificado = signal(false);

  ngOnInit(): void {
    this.verificado.set(this.ruta.snapshot.queryParamMap.get('ok') === '1');
  }
}
