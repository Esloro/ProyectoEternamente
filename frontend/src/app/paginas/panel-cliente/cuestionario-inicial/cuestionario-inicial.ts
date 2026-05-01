import { Component, OnInit, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';

import { BodaService } from '../../../servicios/boda.service';
import {
  DatosCuestionarioInicial,
  FranjaHoraria,
  TipoCeremonia,
  TipoComida,
  Tematica,
  PresupuestoOrientativo,
} from '../../../modelos';

@Component({
  selector: 'app-cuestionario-inicial',
  standalone: true,
  imports: [ReactiveFormsModule],
  templateUrl: './cuestionario-inicial.html',
  styleUrl: './cuestionario-inicial.scss',
})
export class CuestionarioInicial implements OnInit {
  private fb = inject(FormBuilder);
  private bodaService = inject(BodaService);
  private router = inject(Router);

  protected enviando = signal(false);
  protected error = signal('');
  protected pasoActual = signal(1);

  protected form = this.fb.group({
    tipo_ceremonia: ['civil' as TipoCeremonia, Validators.required],
    iglesia: [''],
    fecha_boda: ['', Validators.required],
    num_invitados: [50, [Validators.required, Validators.min(1), Validators.max(2000)]],
    franja_horaria: ['tarde' as FranjaHoraria, Validators.required],
    tematica: ['clasica' as Tematica, Validators.required],
    tipo_comida: ['banquete' as TipoComida, Validators.required],
    presupuesto_orientativo: ['10000_20000' as PresupuestoOrientativo, Validators.required],
  });

  protected readonly tiposCeremonia: { valor: TipoCeremonia; etiqueta: string }[] = [
    { valor: 'civil', etiqueta: 'Civil' },
    { valor: 'iglesia', etiqueta: 'Iglesia' },
    { valor: 'aire_libre', etiqueta: 'Al aire libre' },
    { valor: 'otra', etiqueta: 'Otra' },
  ];

  protected readonly franjas: { valor: FranjaHoraria; etiqueta: string }[] = [
    { valor: 'manana', etiqueta: 'Mañana (antes de las 14h)' },
    { valor: 'tarde', etiqueta: 'Tarde (14h – 20h)' },
    { valor: 'noche', etiqueta: 'Noche (después de las 20h)' },
  ];

  protected readonly tematicas: { valor: Tematica; etiqueta: string; emoji: string }[] = [
    { valor: 'clasica', etiqueta: 'Clásica', emoji: '🌹' },
    { valor: 'rustica', etiqueta: 'Rústica', emoji: '🌾' },
    { valor: 'moderna', etiqueta: 'Moderna', emoji: '✨' },
    { valor: 'boho', etiqueta: 'Boho', emoji: '🌿' },
    { valor: 'glamour', etiqueta: 'Glamour', emoji: '💎' },
  ];

  protected readonly tiposComida: { valor: TipoComida; etiqueta: string; desc: string }[] = [
    { valor: 'coctel', etiqueta: 'Cóctel', desc: 'Estilo informal y dinámico' },
    { valor: 'banquete', etiqueta: 'Banquete', desc: 'Menú de varios platos en mesa' },
    { valor: 'buffet', etiqueta: 'Buffet', desc: 'Variedad libre para todos los gustos' },
    { valor: 'familiar', etiqueta: 'Familiar', desc: 'Platos en el centro de la mesa' },
  ];

  protected readonly presupuestos: { valor: PresupuestoOrientativo; etiqueta: string }[] = [
    { valor: 'hasta_10000', etiqueta: 'Hasta 10.000 €' },
    { valor: '10000_20000', etiqueta: '10.000 € – 20.000 €' },
    { valor: '20000_35000', etiqueta: '20.000 € – 35.000 €' },
    { valor: '35000_50000', etiqueta: '35.000 € – 50.000 €' },
    { valor: 'mas_50000', etiqueta: 'Más de 50.000 €' },
  ];

  protected readonly totalPasos = 4;
  protected readonly Math = Math;

  protected get iglesiaNecesaria(): boolean {
    return this.form.get('tipo_ceremonia')?.value === 'iglesia';
  }

  ngOnInit(): void {
    this.form.get('tipo_ceremonia')?.valueChanges.subscribe((val) => {
      if (val !== 'iglesia') this.form.get('iglesia')?.setValue('');
    });
  }

  protected siguientePaso(): void {
    if (this.pasoActual() < this.totalPasos) this.pasoActual.update((p) => p + 1);
  }

  protected anteriorPaso(): void {
    if (this.pasoActual() > 1) this.pasoActual.update((p) => p - 1);
  }

  protected enviar(): void {
    if (this.form.invalid || this.enviando()) return;
    this.enviando.set(true);
    this.error.set('');

    const datos = this.form.value as DatosCuestionarioInicial;

    this.bodaService.guardarCuestionario(datos).subscribe({
      next: () => this.router.navigate(['/panel/pendiente']),
      error: (e) => {
        this.error.set(e?.error?.message ?? 'Ha ocurrido un error. Inténtalo de nuevo.');
        this.enviando.set(false);
      },
    });
  }
}
