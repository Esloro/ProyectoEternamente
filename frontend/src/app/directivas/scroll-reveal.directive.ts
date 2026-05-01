import { Directive, ElementRef, OnDestroy, OnInit, inject } from '@angular/core';

/**
 * Marca un elemento con la clase `scroll-reveal` y le añade `revealed`
 * cuando entra en el viewport (via IntersectionObserver). Combinado con
 * los estilos globales en styles.scss, hace que la seccion se desvanezca
 * y suba al hacer scroll.
 *
 * Uso:  <section appScrollReveal>...</section>
 */
@Directive({
  selector: '[appScrollReveal]',
  standalone: true,
})
export class ScrollRevealDirective implements OnInit, OnDestroy {
  private el = inject(ElementRef<HTMLElement>);
  private observer?: IntersectionObserver;

  ngOnInit(): void {
    this.el.nativeElement.classList.add('scroll-reveal');

    this.observer = new IntersectionObserver(
      (entradas) => {
        entradas.forEach((entrada) => {
          if (entrada.isIntersecting) {
            entrada.target.classList.add('revealed');
            this.observer?.unobserve(entrada.target);
          }
        });
      },
      { threshold: 0.15 },
    );

    this.observer.observe(this.el.nativeElement);
  }

  ngOnDestroy(): void {
    this.observer?.disconnect();
  }
}
