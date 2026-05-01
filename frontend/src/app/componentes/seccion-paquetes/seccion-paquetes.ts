import { DecimalPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { Router } from '@angular/router';
import { ScrollRevealDirective } from '../../directivas/scroll-reveal.directive';
import { Paquete } from '../../modelos';
import { AutenticacionService } from '../../servicios/autenticacion.service';
import { PaqueteService } from '../../servicios/paquete.service';

@Component({
  selector: 'app-seccion-paquetes',
  standalone: true,
  imports: [ScrollRevealDirective, DecimalPipe],
  templateUrl: './seccion-paquetes.html',
  styleUrl: './seccion-paquetes.scss',
})
export class SeccionPaquetes implements OnInit {
  private servicio = inject(PaqueteService);
  private auth = inject(AutenticacionService);
  private router = inject(Router);

  protected paquetes = signal<Paquete[]>([]);
  protected cargando = signal(true);
  protected error = signal<string | null>(null);

  ngOnInit(): void {
    this.servicio.index().subscribe({
      next: (r) => {
        this.paquetes.set(r.data.paquetes);
        this.cargando.set(false);
      },
      error: () => {
        this.error.set('No se pudieron cargar los paquetes.');
        this.cargando.set(false);
      },
    });
  }

  contratar(): void {
    // Si esta logueado va al panel; si no, le mandamos a registrarse.
    if (this.auth.estaLogueado()) {
      this.router.navigate(['/panel']);
    } else {
      this.router.navigate(['/registro']);
    }
  }
}
