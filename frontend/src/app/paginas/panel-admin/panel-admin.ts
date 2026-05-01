import { Component, inject, signal } from '@angular/core';
import { Router, RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { AutenticacionService } from '../../servicios/autenticacion.service';

@Component({
  selector: 'app-panel-admin',
  standalone: true,
  imports: [RouterOutlet, RouterLink, RouterLinkActive],
  templateUrl: './panel-admin.html',
  styleUrl: './panel-admin.scss',
})
export class PanelAdmin {
  protected auth = inject(AutenticacionService);
  private router = inject(Router);

  protected menuMovilAbierto = signal(false);

  protected cerrarSesion(): void {
    this.auth.logout().subscribe({
      next: () => this.router.navigate(['/login']),
      error: () => this.router.navigate(['/login']),
    });
  }

  protected toggleMenuMovil(): void {
    this.menuMovilAbierto.update((v) => !v);
  }

  protected cerrarMenuMovil(): void {
    this.menuMovilAbierto.set(false);
  }
}
