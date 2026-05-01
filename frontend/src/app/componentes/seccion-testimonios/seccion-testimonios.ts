import { Component, OnDestroy, OnInit, computed, inject, signal } from '@angular/core';
import { ScrollRevealDirective } from '../../directivas/scroll-reveal.directive';
import { Testimonio } from '../../modelos';
import { TestimonioService } from '../../servicios/testimonio.service';

@Component({
  selector: 'app-seccion-testimonios',
  standalone: true,
  imports: [ScrollRevealDirective],
  templateUrl: './seccion-testimonios.html',
  styleUrl: './seccion-testimonios.scss',
})
export class SeccionTestimonios implements OnInit, OnDestroy {
  private servicio = inject(TestimonioService);
  private intervaloId?: ReturnType<typeof setInterval>;

  protected testimonios = signal<Testimonio[]>([]);
  protected indiceActivo = signal(0);

  // Para hacer arrays de longitud N en la plantilla (estrellas).
  protected estrellasLlenas(valoracion: number): number[] {
    return Array(valoracion).fill(0);
  }
  protected estrellasVacias(valoracion: number): number[] {
    return Array(Math.max(0, 5 - valoracion)).fill(0);
  }

  ngOnInit(): void {
    this.servicio.index().subscribe({
      next: (r) => {
        this.testimonios.set(r.data.testimonios);
        if (r.data.testimonios.length > 1) {
          this.iniciarAutoplay();
        }
      },
    });
  }

  ngOnDestroy(): void {
    if (this.intervaloId) clearInterval(this.intervaloId);
  }

  siguiente(): void {
    const total = this.testimonios().length;
    if (total === 0) return;
    this.indiceActivo.update((i) => (i + 1) % total);
  }

  anterior(): void {
    const total = this.testimonios().length;
    if (total === 0) return;
    this.indiceActivo.update((i) => (i - 1 + total) % total);
  }

  irA(indice: number): void {
    this.indiceActivo.set(indice);
    this.reiniciarAutoplay();
  }

  private iniciarAutoplay(): void {
    this.intervaloId = setInterval(() => this.siguiente(), 5000);
  }

  private reiniciarAutoplay(): void {
    if (this.intervaloId) clearInterval(this.intervaloId);
    this.iniciarAutoplay();
  }
}
