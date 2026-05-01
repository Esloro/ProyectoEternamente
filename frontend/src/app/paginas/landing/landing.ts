import { Component } from '@angular/core';
import { Footer } from '../../componentes/footer/footer';
import { Hero } from '../../componentes/hero/hero';
import { Navbar } from '../../componentes/navbar/navbar';
import { SeccionContacto } from '../../componentes/seccion-contacto/seccion-contacto';
import { SeccionPaquetes } from '../../componentes/seccion-paquetes/seccion-paquetes';
import { SeccionPortfolio } from '../../componentes/seccion-portfolio/seccion-portfolio';
import { SeccionSobreNosotros } from '../../componentes/seccion-sobre-nosotros/seccion-sobre-nosotros';
import { SeccionTestimonios } from '../../componentes/seccion-testimonios/seccion-testimonios';

/**
 * Landing publica. Compone navbar fijo + 7 secciones (hero, paquetes,
 * portfolio, testimonios, sobre nosotros, contacto) + footer.
 */
@Component({
  selector: 'app-landing',
  standalone: true,
  imports: [
    Navbar,
    Hero,
    SeccionPaquetes,
    SeccionPortfolio,
    SeccionTestimonios,
    SeccionSobreNosotros,
    SeccionContacto,
    Footer,
  ],
  templateUrl: './landing.html',
  styleUrl: './landing.scss',
})
export class Landing {}
