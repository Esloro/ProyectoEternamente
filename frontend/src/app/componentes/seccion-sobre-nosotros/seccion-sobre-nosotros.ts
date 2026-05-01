import { Component } from '@angular/core';
import { ScrollRevealDirective } from '../../directivas/scroll-reveal.directive';

@Component({
  selector: 'app-seccion-sobre-nosotros',
  standalone: true,
  imports: [ScrollRevealDirective],
  templateUrl: './seccion-sobre-nosotros.html',
  styleUrl: './seccion-sobre-nosotros.scss',
})
export class SeccionSobreNosotros {}
