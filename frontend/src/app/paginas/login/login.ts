import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { AutenticacionService } from '../../servicios/autenticacion.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './login.html',
  styleUrl: './login.scss',
})
export class Login {
  private fb = inject(FormBuilder);
  private auth = inject(AutenticacionService);
  private router = inject(Router);
  private ruta = inject(ActivatedRoute);

  protected enviando = signal(false);
  protected error = signal<string | null>(null);

  protected formulario = this.fb.nonNullable.group({
    email: ['', [Validators.required, Validators.email]],
    password: ['', [Validators.required]],
  });

  enviar(): void {
    if (this.formulario.invalid) {
      this.formulario.markAllAsTouched();
      return;
    }

    this.enviando.set(true);
    this.error.set(null);

    this.auth.login(this.formulario.getRawValue()).subscribe({
      next: () => {
        // Redirigimos al returnUrl (si vino de un guard) o al panel correspondiente.
        const returnUrl = this.ruta.snapshot.queryParamMap.get('returnUrl');
        if (returnUrl) {
          this.router.navigateByUrl(returnUrl);
        } else if (this.auth.esAdministrador()) {
          this.router.navigate(['/admin']);
        } else {
          this.router.navigate(['/panel']);
        }
      },
      error: (err) => {
        this.enviando.set(false);
        this.error.set(err?.error?.message ?? 'No se pudo iniciar sesión.');
      },
    });
  }

  campoInvalido(nombre: string): boolean {
    const c = this.formulario.get(nombre);
    return !!c && c.invalid && (c.dirty || c.touched);
  }
}
