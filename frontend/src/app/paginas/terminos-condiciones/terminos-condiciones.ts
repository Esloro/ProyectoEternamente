import { Component } from '@angular/core';
import { Footer } from '../../componentes/footer/footer';
import { Navbar } from '../../componentes/navbar/navbar';

@Component({
  selector: 'app-terminos-condiciones',
  standalone: true,
  imports: [Navbar, Footer],
  templateUrl: './terminos-condiciones.html',
  styleUrl: './terminos-condiciones.scss',
})
export class TerminosCondiciones {
  protected fechaActualizacion = '14 de mayo de 2026';
}
