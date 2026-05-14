import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';

import { AutenticacionService } from '../../../servicios/autenticacion.service';
import { BodaService } from '../../../servicios/boda.service';
import { UsuarioService } from '../../../servicios/usuario.service';

@Component({
  selector: 'app-mi-perfil',
  standalone: true,
  imports: [ReactiveFormsModule],
  templateUrl: './mi-perfil.html',
  styleUrl: './mi-perfil.scss',
})
export class MiPerfil implements OnInit {
  protected auth = inject(AutenticacionService);
  protected bodaService = inject(BodaService);
  private usuarioService = inject(UsuarioService);
  private fb = inject(FormBuilder);

  protected guardandoPerfil = signal(false);
  protected guardandoPassword = signal(false);
  protected exitoPerfil = signal('');
  protected exitoPassword = signal('');
  protected errorPerfil = signal('');
  protected errorPassword = signal('');

  // Eliminacion de cuenta
  protected modalEliminarAbierto = signal(false);
  protected aceptaCondiciones = signal(false);
  protected enviandoEliminacion = signal(false);
  protected exitoEliminacion = signal('');
  protected errorEliminacion = signal('');

  // Bloqueamos la auto-eliminacion si la boda esta activa o finalizada:
  // en esos casos el usuario tiene que contactar con el administrador.
  protected bodaBloqueaEliminacion = computed(() => {
    const estado = this.bodaService.bodaActual()?.estado;
    return estado === 'activa' || estado === 'finalizada';
  });

  protected formPerfil = this.fb.group({
    nombre: ['', [Validators.required, Validators.minLength(2)]],
    apellidos: ['', [Validators.required, Validators.minLength(2)]],
    telefono: [''],
  });

  protected formPassword = this.fb.group(
    {
      password_actual: ['', Validators.required],
      password: ['', [Validators.required, Validators.minLength(8)]],
      password_confirmation: ['', Validators.required],
    },
    { validators: this.passwordsCoinciden },
  );

  private passwordsCoinciden(group: import('@angular/forms').AbstractControl) {
    const pass = group.get('password')?.value;
    const conf = group.get('password_confirmation')?.value;
    return pass === conf ? null : { noCoinciden: true };
  }

  ngOnInit(): void {
    const u = this.auth.usuario();
    if (u) {
      this.formPerfil.patchValue({
        nombre: u.nombre,
        apellidos: u.apellidos,
        telefono: u.telefono ?? '',
      });
    }
    // Refrescamos la boda actual para que `bodaBloqueaEliminacion` este al dia.
    this.bodaService.miBoda().subscribe();
  }

  protected guardarPerfil(): void {
    if (this.formPerfil.invalid || this.guardandoPerfil()) return;
    this.guardandoPerfil.set(true);
    this.exitoPerfil.set('');
    this.errorPerfil.set('');

    const { nombre, apellidos, telefono } = this.formPerfil.value;
    this.usuarioService.actualizarPerfil({ nombre: nombre!, apellidos: apellidos!, telefono: telefono || null }).subscribe({
      next: (r) => {
        this.exitoPerfil.set('Perfil actualizado correctamente.');
        this.guardandoPerfil.set(false);
        setTimeout(() => this.exitoPerfil.set(''), 3000);
      },
      error: (e) => {
        this.errorPerfil.set(e?.error?.message ?? 'Error al actualizar el perfil.');
        this.guardandoPerfil.set(false);
      },
    });
  }

  protected cambiarPassword(): void {
    if (this.formPassword.invalid || this.guardandoPassword()) return;
    this.guardandoPassword.set(true);
    this.exitoPassword.set('');
    this.errorPassword.set('');

    const { password_actual, password, password_confirmation } = this.formPassword.value;
    this.usuarioService.cambiarPassword({
      password_actual: password_actual!,
      password: password!,
      password_confirmation: password_confirmation!,
    }).subscribe({
      next: () => {
        this.exitoPassword.set('Contraseña cambiada correctamente.');
        this.formPassword.reset();
        this.guardandoPassword.set(false);
        setTimeout(() => this.exitoPassword.set(''), 4000);
      },
      error: (e) => {
        this.errorPassword.set(e?.error?.message ?? 'Error al cambiar la contraseña.');
        this.guardandoPassword.set(false);
      },
    });
  }

  protected abrirModalEliminar(): void {
    this.aceptaCondiciones.set(false);
    this.errorEliminacion.set('');
    this.exitoEliminacion.set('');
    this.modalEliminarAbierto.set(true);
  }

  protected cerrarModalEliminar(): void {
    if (this.enviandoEliminacion()) return;
    this.modalEliminarAbierto.set(false);
  }

  protected confirmarSolicitudEliminacion(): void {
    if (!this.aceptaCondiciones() || this.enviandoEliminacion()) return;
    this.enviandoEliminacion.set(true);
    this.errorEliminacion.set('');

    this.auth.solicitarEliminacionCuenta().subscribe({
      next: () => {
        this.enviandoEliminacion.set(false);
        this.exitoEliminacion.set(
          'Te hemos enviado un email para confirmar la eliminación. Revisa tu bandeja de entrada.',
        );
      },
      error: (e) => {
        this.enviandoEliminacion.set(false);
        this.errorEliminacion.set(
          e?.error?.message ?? 'No hemos podido enviar el email. Inténtalo más tarde.',
        );
      },
    });
  }
}
