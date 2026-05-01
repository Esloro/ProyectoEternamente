import { Component, OnInit, inject, signal } from '@angular/core';
import { AbstractControl, FormBuilder, ReactiveFormsModule, ValidationErrors, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { AutenticacionService } from '../../servicios/autenticacion.service';

type Modo = 'solicitar' | 'reset';

function passwordsCoinciden(grupo: AbstractControl): ValidationErrors | null {
  const p = grupo.get('password')?.value;
  const c = grupo.get('password_confirmation')?.value;
  return p && c && p !== c ? { passwordsNoCoinciden: true } : null;
}

/**
 * Pagina /recuperar-password con dos modos:
 *
 *  1. solicitar (default): formulario con email -> envia link al correo.
 *  2. reset (cuando llega ?token=X&email=Y por URL desde el email):
 *     formulario para fijar nueva contraseña.
 */
@Component({
  selector: 'app-recuperar-password',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './recuperar-password.html',
  styleUrl: './recuperar-password.scss',
})
export class RecuperarPassword implements OnInit {
  private fb = inject(FormBuilder);
  private auth = inject(AutenticacionService);
  private ruta = inject(ActivatedRoute);
  private router = inject(Router);

  protected modo = signal<Modo>('solicitar');
  protected enviando = signal(false);
  protected exito = signal(false);
  protected error = signal<string | null>(null);

  protected formSolicitud = this.fb.nonNullable.group({
    email: ['', [Validators.required, Validators.email]],
  });

  protected formReset = this.fb.nonNullable.group(
    {
      token: ['', [Validators.required]],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(8), Validators.pattern(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/)]],
      password_confirmation: ['', [Validators.required]],
    },
    { validators: passwordsCoinciden },
  );

  ngOnInit(): void {
    const params = this.ruta.snapshot.queryParamMap;
    const token = params.get('token');
    const email = params.get('email');
    if (token && email) {
      this.modo.set('reset');
      this.formReset.patchValue({ token, email });
    }
  }

  enviarSolicitud(): void {
    if (this.formSolicitud.invalid) {
      this.formSolicitud.markAllAsTouched();
      return;
    }

    this.enviando.set(true);
    this.error.set(null);

    this.auth.solicitarRecuperacion(this.formSolicitud.value.email!).subscribe({
      next: () => {
        this.exito.set(true);
        this.enviando.set(false);
      },
      error: (err) => {
        this.enviando.set(false);
        this.error.set(err?.error?.message ?? 'No se pudo procesar la solicitud.');
      },
    });
  }

  enviarReset(): void {
    if (this.formReset.invalid) {
      this.formReset.markAllAsTouched();
      return;
    }

    this.enviando.set(true);
    this.error.set(null);

    const { token, email, password, password_confirmation } = this.formReset.getRawValue();
    this.auth.resetearPassword(token, email, password, password_confirmation).subscribe({
      next: () => {
        this.exito.set(true);
        this.enviando.set(false);
        setTimeout(() => this.router.navigate(['/login']), 3000);
      },
      error: (err) => {
        this.enviando.set(false);
        this.error.set(err?.error?.message ?? 'El enlace puede haber caducado. Solicita uno nuevo.');
      },
    });
  }

  campoInvalidoSolicitud(nombre: string): boolean {
    const c = this.formSolicitud.get(nombre);
    return !!c && c.invalid && (c.dirty || c.touched);
  }

  campoInvalidoReset(nombre: string): boolean {
    const c = this.formReset.get(nombre);
    return !!c && c.invalid && (c.dirty || c.touched);
  }

  passwordsNoCoinciden(): boolean {
    return (
      this.formReset.errors?.['passwordsNoCoinciden'] === true &&
      !!this.formReset.get('password_confirmation')?.touched
    );
  }
}
