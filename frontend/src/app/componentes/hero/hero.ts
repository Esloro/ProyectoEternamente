import { Component } from '@angular/core';

@Component({
  selector: 'app-hero',
  standalone: true,
  imports: [],
  templateUrl: './hero.html',
  styleUrl: './hero.scss',
})
export class Hero {
  protected scrollAPaquetes(event: Event): void {
    event.preventDefault();
    const seccion = document.getElementById('paquetes');
    seccion?.scrollIntoView({ behavior: 'smooth' });
  }
}
