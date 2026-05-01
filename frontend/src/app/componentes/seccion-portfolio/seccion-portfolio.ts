import { Component, OnInit, inject, signal } from '@angular/core';
import { ScrollRevealDirective } from '../../directivas/scroll-reveal.directive';
import { Portfolio } from '../../modelos';
import { PortfolioService } from '../../servicios/portfolio.service';

@Component({
  selector: 'app-seccion-portfolio',
  standalone: true,
  imports: [ScrollRevealDirective],
  templateUrl: './seccion-portfolio.html',
  styleUrl: './seccion-portfolio.scss',
})
export class SeccionPortfolio implements OnInit {
  private servicio = inject(PortfolioService);

  protected entradas = signal<Portfolio[]>([]);
  protected cargando = signal(true);
  protected entradaActiva = signal<Portfolio | null>(null);

  ngOnInit(): void {
    this.servicio.index().subscribe({
      next: (r) => {
        this.entradas.set(r.data.portfolio);
        this.cargando.set(false);
      },
      error: () => this.cargando.set(false),
    });
  }

  abrirModal(entrada: Portfolio): void {
    this.entradaActiva.set(entrada);
    document.body.style.overflow = 'hidden';
  }

  cerrarModal(): void {
    this.entradaActiva.set(null);
    document.body.style.overflow = '';
  }
}
