import { Component, OnInit, inject, signal } from '@angular/core';
import {
  CdkDragDrop,
  DragDropModule,
  moveItemInArray,
  transferArrayItem,
} from '@angular/cdk/drag-drop';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';

import { InvitadoService, DatosInvitado } from '../../../servicios/invitado.service';
import { MesaService, DatosMesa } from '../../../servicios/mesa.service';
import { Invitado, Mesa } from '../../../modelos';

interface MesaConInvitados extends Mesa {
  invitadosLista: Invitado[];
}

@Component({
  selector: 'app-organizador-mesas',
  standalone: true,
  imports: [DragDropModule, ReactiveFormsModule],
  templateUrl: './organizador-mesas.html',
  styleUrl: './organizador-mesas.scss',
})
export class OrganizadorMesas implements OnInit {
  private invitadoService = inject(InvitadoService);
  private mesaService = inject(MesaService);
  private fb = inject(FormBuilder);

  protected mesas = signal<MesaConInvitados[]>([]);
  protected sinMesa = signal<Invitado[]>([]);
  protected cargando = signal(true);
  protected error = signal('');

  protected modalMesa = signal(false);
  protected modalInvitado = signal(false);
  protected editandoMesa = signal<Mesa | null>(null);
  protected editandoInvitado = signal<Invitado | null>(null);
  protected guardando = signal(false);

  protected formMesa = this.fb.group({
    numero: [1, [Validators.required, Validators.min(1)]],
    capacidad: [8, [Validators.required, Validators.min(1), Validators.max(100)]],
  });

  protected formInvitado = this.fb.group({
    nombre: ['', [Validators.required, Validators.minLength(2)]],
    alergias: [''],
    acompanante: [false],
  });

  ngOnInit(): void {
    this.cargarDatos();
  }

  private cargarDatos(): void {
    this.cargando.set(true);
    Promise.all([
      this.mesaService.index().toPromise(),
      this.invitadoService.index().toPromise(),
    ]).then(([mesasRes, invRes]) => {
      const todosInvitados = invRes?.data.invitados ?? [];
      const todasMesas = mesasRes?.data.mesas ?? [];

      const mesasConLista: MesaConInvitados[] = todasMesas.map((m) => ({
        ...m,
        invitadosLista: todosInvitados.filter((i) => i.mesa_id === m.id),
      }));

      this.mesas.set(mesasConLista);
      this.sinMesa.set(todosInvitados.filter((i) => i.mesa_id === null));
      this.cargando.set(false);
    }).catch(() => this.cargando.set(false));
  }

  // ── Drag & Drop ──────────────────────────────────────────────────
  protected get dropListIds(): string[] {
    return ['sin-mesa', ...this.mesas().map((m) => `mesa-${m.id}`)];
  }

  protected drop(event: CdkDragDrop<Invitado[]>, mesaId: number | null): void {
    if (event.previousContainer === event.container) {
      moveItemInArray(event.container.data, event.previousIndex, event.currentIndex);
    } else {
      const invitado = event.previousContainer.data[event.previousIndex];
      transferArrayItem(
        event.previousContainer.data,
        event.container.data,
        event.previousIndex,
        event.currentIndex,
      );

      this.invitadoService.asignarMesa(invitado.id, mesaId).subscribe({
        error: () => this.cargarDatos(),
      });
    }
  }

  // ── Mesas ────────────────────────────────────────────────────────
  protected abrirModalMesa(mesa?: Mesa): void {
    this.editandoMesa.set(mesa ?? null);
    if (mesa) {
      this.formMesa.setValue({ numero: mesa.numero, capacidad: mesa.capacidad });
    } else {
      const siguienteNumero = this.mesas().length + 1;
      this.formMesa.setValue({ numero: siguienteNumero, capacidad: 8 });
    }
    this.modalMesa.set(true);
  }

  protected cerrarModalMesa(): void {
    this.modalMesa.set(false);
    this.editandoMesa.set(null);
    this.formMesa.reset({ numero: 1, capacidad: 8 });
  }

  protected guardarMesa(): void {
    if (this.formMesa.invalid || this.guardando()) return;
    this.guardando.set(true);
    const datos = this.formMesa.value as DatosMesa;
    const mesa = this.editandoMesa();

    const obs = mesa
      ? this.mesaService.actualizar(mesa.id, datos)
      : this.mesaService.crear(datos);

    obs.subscribe({
      next: () => { this.cerrarModalMesa(); this.cargarDatos(); this.guardando.set(false); },
      error: (e) => { this.error.set(e?.error?.message ?? 'Error al guardar mesa.'); this.guardando.set(false); },
    });
  }

  protected eliminarMesa(id: number): void {
    if (!confirm('¿Eliminar esta mesa? Los invitados asignados pasarán a "Sin mesa".')) return;
    this.mesaService.eliminar(id).subscribe({
      next: () => this.cargarDatos(),
      error: (e) => this.error.set(e?.error?.message ?? 'Error al eliminar.'),
    });
  }

  // ── Invitados ────────────────────────────────────────────────────
  protected abrirModalInvitado(invitado?: Invitado): void {
    this.editandoInvitado.set(invitado ?? null);
    if (invitado) {
      this.formInvitado.setValue({
        nombre: invitado.nombre,
        alergias: invitado.alergias ?? '',
        acompanante: invitado.acompanante,
      });
    } else {
      this.formInvitado.reset({ acompanante: false });
    }
    this.modalInvitado.set(true);
  }

  protected cerrarModalInvitado(): void {
    this.modalInvitado.set(false);
    this.editandoInvitado.set(null);
    this.formInvitado.reset({ acompanante: false });
  }

  protected guardarInvitado(): void {
    if (this.formInvitado.invalid || this.guardando()) return;
    this.guardando.set(true);
    const datos = this.formInvitado.value as DatosInvitado;
    const inv = this.editandoInvitado();

    const obs = inv
      ? this.invitadoService.actualizar(inv.id, datos)
      : this.invitadoService.crear(datos);

    obs.subscribe({
      next: () => { this.cerrarModalInvitado(); this.cargarDatos(); this.guardando.set(false); },
      error: (e) => { this.error.set(e?.error?.message ?? 'Error al guardar invitado.'); this.guardando.set(false); },
    });
  }

  protected eliminarInvitado(id: number): void {
    if (!confirm('¿Eliminar este invitado?')) return;
    this.invitadoService.eliminar(id).subscribe({
      next: () => this.cargarDatos(),
      error: (e) => this.error.set(e?.error?.message ?? 'Error al eliminar.'),
    });
  }

  protected readonly Math = Math;

  protected totalInvitados(): number {
    return this.sinMesa().length + this.mesas().reduce((s, m) => s + m.invitadosLista.length, 0);
  }
}
