import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { DecimalPipe, DatePipe } from '@angular/common';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { RouterLink } from '@angular/router';

import { BodaService } from '../../../servicios/boda.service';
import {
  Boda,
  DatosCuestionarioInicial,
  ETIQUETAS_CEREMONIA,
  ETIQUETAS_ESTADO,
  ETIQUETAS_FRANJA,
  ETIQUETAS_LUGAR,
  ETIQUETAS_TEMATICA,
  ETIQUETAS_COMIDA,
  ETIQUETAS_PRESUPUESTO,
  FranjaHoraria,
  LugarCelebracion,
  PresupuestoOrientativo,
  Tematica,
  TipoCeremonia,
  TipoComida,
} from '../../../modelos';

@Component({
  selector: 'app-resumen-boda',
  standalone: true,
  imports: [RouterLink, DecimalPipe, DatePipe, ReactiveFormsModule],
  templateUrl: './resumen-boda.html',
  styleUrl: './resumen-boda.scss',
})
export class ResumenBoda implements OnInit {
  private bodaService = inject(BodaService);
  private fb = inject(FormBuilder);

  protected boda = signal<Boda | null>(null);
  protected cargando = signal(true);

  protected cuentaAtras = computed<number | null>(() => {
    const fecha = this.boda()?.fecha_boda;
    if (!fecha) return null;
    const objetivo = new Date(fecha);
    if (Number.isNaN(objetivo.getTime())) return null;
    objetivo.setHours(0, 0, 0, 0);
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    const dias = Math.round((objetivo.getTime() - hoy.getTime()) / 86_400_000);
    return Math.max(0, dias);
  });

  protected editando = signal(false);
  protected guardando = signal(false);
  protected mensajeExito = signal('');
  protected error = signal('');

  protected readonly etiquetasEstado = ETIQUETAS_ESTADO;
  protected readonly etiquetasCeremonia = ETIQUETAS_CEREMONIA;
  protected readonly etiquetasLugar = ETIQUETAS_LUGAR;
  protected readonly etiquetasFranja = ETIQUETAS_FRANJA;
  protected readonly etiquetasTematica = ETIQUETAS_TEMATICA;
  protected readonly etiquetasComida = ETIQUETAS_COMIDA;
  protected readonly etiquetasPresupuesto = ETIQUETAS_PRESUPUESTO;

  protected readonly tiposCeremonia: { valor: TipoCeremonia; etiqueta: string }[] = [
    { valor: 'religiosa', etiqueta: 'Religiosa' },
    { valor: 'civil_ayuntamiento', etiqueta: 'Civil ayuntamiento/juzgado' },
    { valor: 'simbolica', etiqueta: 'Ceremonia simbólica' },
    { valor: 'renovacion_votos', etiqueta: 'Renovación de votos' },
  ];

  protected readonly lugaresCelebracion: { valor: LugarCelebracion; etiqueta: string }[] = [
    { valor: 'iglesia', etiqueta: 'Iglesia' },
    { valor: 'ayuntamiento', etiqueta: 'Ayuntamiento/juzgado' },
    { valor: 'finca', etiqueta: 'Finca o hacienda' },
    { valor: 'playa', etiqueta: 'Playa' },
    { valor: 'jardin', etiqueta: 'Jardín/Mirador' },
    { valor: 'restaurante', etiqueta: 'Restaurante' },
    { valor: 'otro', etiqueta: 'Otro' },
  ];

  protected readonly franjas: { valor: FranjaHoraria; etiqueta: string }[] = [
    { valor: 'manana', etiqueta: 'Mañana' },
    { valor: 'tarde', etiqueta: 'Tarde' },
    { valor: 'noche', etiqueta: 'Noche' },
  ];

  protected readonly tematicas: { valor: Tematica; etiqueta: string }[] = [
    { valor: 'clasica', etiqueta: 'Clásica' },
    { valor: 'rustica', etiqueta: 'Rústica' },
    { valor: 'moderna', etiqueta: 'Moderna' },
    { valor: 'boho', etiqueta: 'Boho' },
    { valor: 'glamour', etiqueta: 'Glamour' },
  ];

  protected readonly tiposComida: { valor: TipoComida; etiqueta: string }[] = [
    { valor: 'coctel', etiqueta: 'Cóctel' },
    { valor: 'banquete', etiqueta: 'Banquete' },
    { valor: 'buffet', etiqueta: 'Buffet' },
    { valor: 'familiar', etiqueta: 'Familiar' },
  ];

  protected readonly presupuestos: { valor: PresupuestoOrientativo; etiqueta: string }[] = [
    { valor: 'hasta_10000', etiqueta: 'Hasta 10.000 €' },
    { valor: '10000_20000', etiqueta: '10.000 € – 20.000 €' },
    { valor: '20000_35000', etiqueta: '20.000 € – 35.000 €' },
    { valor: '35000_50000', etiqueta: '35.000 € – 50.000 €' },
    { valor: 'mas_50000', etiqueta: 'Más de 50.000 €' },
  ];

  protected form = this.fb.group({
    nombre_pareja: ['', [Validators.required, Validators.maxLength(150)]],
    tipo_ceremonia: ['religiosa' as TipoCeremonia, Validators.required],
    lugar_celebracion: ['iglesia' as LugarCelebracion, Validators.required],
    fecha_boda: ['', Validators.required],
    num_invitados: [50, [Validators.required, Validators.min(1), Validators.max(2000)]],
    franja_horaria: ['tarde' as FranjaHoraria, Validators.required],
    tematica: ['clasica' as Tematica, Validators.required],
    tipo_comida: ['banquete' as TipoComida, Validators.required],
    presupuesto_orientativo: ['10000_20000' as PresupuestoOrientativo, Validators.required],
  });

  protected editable = computed(() => {
    const estado = this.boda()?.estado;
    return estado === 'pendiente_reunion' || estado === 'activa';
  });

  ngOnInit(): void {
    this.bodaService.miBoda().subscribe({
      next: (r) => {
        this.boda.set(r.data.boda);
        this.cargando.set(false);
      },
      error: () => this.cargando.set(false),
    });
  }

  protected iniciarEdicion(): void {
    const b = this.boda();
    if (!b) return;
    this.form.reset({
      nombre_pareja: b.nombre_pareja ?? '',
      tipo_ceremonia: b.tipo_ceremonia,
      lugar_celebracion: b.lugar_celebracion,
      fecha_boda: this.aFechaInput(b.fecha_boda),
      num_invitados: b.num_invitados,
      franja_horaria: b.franja_horaria,
      tematica: b.tematica,
      tipo_comida: b.tipo_comida,
      presupuesto_orientativo: b.presupuesto_orientativo,
    });
    this.error.set('');
    this.mensajeExito.set('');
    this.editando.set(true);
  }

  /**
   * Normaliza la fecha que llega del backend a 'YYYY-MM-DD' para el
   * input type=date. Funciona tanto si viene como ISO ("YYYY-MM-DDT...")
   * como si viene ya en formato corto.
   */
  private aFechaInput(fecha: string | null | undefined): string {
    if (!fecha) return '';
    return fecha.substring(0, 10);
  }

  protected cancelarEdicion(): void {
    this.editando.set(false);
    this.error.set('');
  }

  protected guardarDetalles(): void {
    if (this.form.invalid || this.guardando()) return;
    this.guardando.set(true);
    this.error.set('');

    const datos = this.form.value as DatosCuestionarioInicial;

    this.bodaService.actualizarDetalles(datos).subscribe({
      next: (r) => {
        this.boda.set(r.data.boda);
        this.editando.set(false);
        this.guardando.set(false);
        this.mensajeExito.set('Detalles actualizados correctamente.');
        setTimeout(() => this.mensajeExito.set(''), 3000);
      },
      error: (e) => {
        const detalle = this.formatearErroresValidacion(e?.error?.data);
        const base = e?.error?.message ?? 'No se han podido guardar los cambios.';
        this.error.set(detalle ? `${base} ${detalle}` : base);
        this.guardando.set(false);
      },
    });
  }

  /**
   * Convierte el objeto { campo: ['mensaje', ...] } que devuelve Laravel
   * cuando falla la validacion en una linea legible. Si no hay errores
   * de campo (p.ej. 500), devuelve cadena vacia.
   */
  private formatearErroresValidacion(data: unknown): string {
    if (!data || typeof data !== 'object') return '';
    const mensajes: string[] = [];
    for (const lista of Object.values(data as Record<string, unknown>)) {
      if (Array.isArray(lista)) {
        for (const m of lista) if (typeof m === 'string') mensajes.push(m);
      }
    }
    return mensajes.join(' ');
  }
}
