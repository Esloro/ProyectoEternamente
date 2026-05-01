import { Component, inject, signal } from '@angular/core';
import { AbstractControl, FormBuilder, ReactiveFormsModule, ValidationErrors, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AutenticacionService } from '../../servicios/autenticacion.service';

/**
 * Validador cruzado: comprueba que `password` y `password_confirmation`
 * coincidan. Se aplica a nivel de FormGroup.
 */
function passwordsCoinciden(grupo: AbstractControl): ValidationErrors | null {
  const p = grupo.get('password')?.value;
  const c = grupo.get('password_confirmation')?.value;
  return p && c && p !== c ? { passwordsNoCoinciden: true } : null;
}

@Component({
  selector: 'app-registro',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './registro.html',
  styleUrl: './registro.scss',
})
export class Registro {
  private fb = inject(FormBuilder);
  private auth = inject(AutenticacionService);
  private router = inject(Router);

  protected enviando = signal(false);
  protected error = signal<string | null>(null);
  protected exito = signal(false);

  protected formulario = this.fb.nonNullable.group(
    {
      nombre:    ['', [Validators.required, Validators.maxLength(100)]],
      apellidos: ['', [Validators.required, Validators.maxLength(150)]],
      email:     ['', [Validators.required, Validators.email, Validators.maxLength(150)]],
      telefono:  [''],
      password:  ['', [Validators.required, Validators.minLength(8), Validators.pattern(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/)]],
      password_confirmation: ['', [Validators.required]],
    },
    { validators: passwordsCoinciden },
  );

  enviar(): void {
    if (this.formulario.invalid) {
      this.formulario.markAllAsTouched();
      return;
    }

    this.enviando.set(true);
    this.error.set(null);

    this.auth.registro(this.formulario.getRawValue()).subscribe({
      next: () => {
        this.exito.set(true);
        this.enviando.set(false);
        // Tras 4 segundos vamos al panel (la cuenta esta creada y logueada
        // con token, pero el panel mostrara el aviso de verificar email).
        setTimeout(() => this.router.navigate(['/panel']), 4000);
      },
      error: (err) => {
        this.enviando.set(false);
        // Si vienen errores de validacion del backend, los mostramos.
        const errores = err?.error?.data;
        if (errores && typeof errores === 'object') {
          const primer = Object.values(errores)[0] as string[] | undefined;
          this.error.set(primer?.[0] ?? err?.error?.message ?? 'No se pudo crear la cuenta.');
        } else {
          this.error.set(err?.error?.message ?? 'No se pudo crear la cuenta.');
        }
      },
    });
  }

  campoInvalido(nombre: string): boolean {
    const c = this.formulario.get(nombre);
    return !!c && c.invalid && (c.dirty || c.touched);
  }

  passwordsNoCoinciden(): boolean {
    return (
      this.formulario.errors?.['passwordsNoCoinciden'] === true &&
      !!this.formulario.get('password_confirmation')?.touched
    );
  }
}
