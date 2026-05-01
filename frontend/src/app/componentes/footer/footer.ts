import { Component } from '@angular/core';

@Component({
  selector: 'app-footer',
  standalone: true,
  imports: [],
  templateUrl: './footer.html',
  styleUrl: './footer.scss',
})
export class Footer {
  protected anyo = new Date().getFullYear();

  protected enlaces = [
    { texto: 'Inicio',          ancla: '#inicio' },
    { texto: 'Paquetes',        ancla: '#paquetes' },
    { texto: 'Portfolio',       ancla: '#portfolio' },
    { texto: 'Testimonios',     ancla: '#testimonios' },
    { texto: 'Sobre Nosotros',  ancla: '#sobre-nosotros' },
    { texto: 'Contacto',        ancla: '#contacto' },
  ];
}
