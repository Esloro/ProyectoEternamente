import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ScrollRevealDirective } from '../../directivas/scroll-reveal.directive';
import { MensajeContactoService } from '../../servicios/mensaje-contacto.service';

type EstadoEnvio = 'inactivo' | 'enviando' | 'exito' | 'error';

@Component({
  selector: 'app-seccion-contacto',
  standalone: true,
  imports: [ReactiveFormsModule, ScrollRevealDirective],
  templateUrl: './seccion-contacto.html',
  styleUrl: './seccion-contacto.scss',
})
export class SeccionContacto {
  private fb = inject(FormBuilder);
  private servicio = inject(MensajeContactoService);

  protected estado = signal<EstadoEnvio>('inactivo');
  protected mensajeError = signal<string>('');

  protected formulario = this.fb.nonNullable.group({
    nombre:   ['', [Validators.required, Validators.maxLength(100)]],
    email:    ['', [Validators.required, Validators.email, Validators.maxLength(150)]],
    telefono: [''],
    mensaje:  ['', [Validators.required, Validators.minLength(10), Validators.maxLength(2000)]],
  });

  enviar(): void {
    if (this.formulario.invalid) {
      this.formulario.markAllAsTouched();
      return;
    }

    this.estado.set('enviando');
    this.mensajeError.set('');

    this.servicio.enviar(this.formulario.getRawValue()).subscribe({
      next: () => {
        this.estado.set('exito');
        this.formulario.reset();
        // Volvemos al estado neutro tras unos segundos.
        setTimeout(() => this.estado.set('inactivo'), 6000);
      },
      error: (err) => {
        this.estado.set('error');
        this.mensajeError.set(err?.error?.message ?? 'No se pudo enviar el mensaje. Intentalo mas tarde.');
      },
    });
  }

  campoInvalido(nombre: string): boolean {
    const c = this.formulario.get(nombre);
    return !!c && c.invalid && (c.dirty || c.touched);
  }
}
