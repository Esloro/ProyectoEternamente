import { Component, HostListener, computed, inject, input, signal } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { AutenticacionService } from '../../servicios/autenticacion.service';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './navbar.html',
  styleUrl: './navbar.scss',
})
export class Navbar {
  private auth = inject(AutenticacionService);
  private router = inject(Router);

  // Cuando se usa la navbar fuera de la landing (paginas sin hero), forzamos
  // el modo opaco para que el texto sea legible sobre fondo claro.
  siempreOpaca = input(false);

  // Signal que cambia segun la posicion de scroll para aplicar fondo solido.
  private scrolleadoInterno = signal(false);
  protected scrolleado = computed(() => this.siempreOpaca() || this.scrolleadoInterno());
  protected menuAbierto = signal(false);

  protected estaLogueado = this.auth.estaLogueado;
  protected esAdministrador = this.auth.esAdministrador;
  protected nombreUsuario = computed(() => this.auth.usuario()?.nombre ?? '');

  protected enlaces = [
    { texto: 'Inicio',          ancla: 'inicio' },
    { texto: 'Paquetes',        ancla: 'paquetes' },
    { texto: 'Portfolio',       ancla: 'portfolio' },
    { texto: 'Testimonios',     ancla: 'testimonios' },
    { texto: 'Sobre Nosotros',  ancla: 'sobre-nosotros' },
    { texto: 'Contacto',        ancla: 'contacto' },
  ];

  @HostListener('window:scroll')
  alHacerScroll(): void {
    this.scrolleadoInterno.set(window.scrollY > 60);
  }

  irAPanel(): void {
    this.menuAbierto.set(false);
    this.router.navigate([this.esAdministrador() ? '/admin' : '/panel']);
  }

  cerrarSesion(): void {
    this.menuAbierto.set(false);
    this.auth.logout().subscribe({
      next: () => this.router.navigate(['/']),
      error: () => this.router.navigate(['/']), // si falla la peticion limpiamos local igual
    });
  }

  alternarMenu(): void {
    this.menuAbierto.update((v) => !v);
  }

  cerrarMenuMovil(): void {
    this.menuAbierto.set(false);
  }
}
