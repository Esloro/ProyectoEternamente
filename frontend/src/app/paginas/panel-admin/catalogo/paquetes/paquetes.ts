import { Component, OnInit, inject, signal } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';

import { AdminService } from '../../../../servicios/admin.service';
import { Paquete } from '../../../../modelos';

@Component({
  selector: 'app-paquetes-admin',
  standalone: true,
  imports: [DecimalPipe, ReactiveFormsModule],
  templateUrl: './paquetes.html',
  styleUrl: './paquetes.scss',
})
export class PaquetesAdmin implements OnInit {
  private adminService = inject(AdminService);
  private fb = inject(FormBuilder);

  protected paquetes = signal<Paquete[]>([]);
  protected cargando = signal(true);
  protected guardando = signal(false);
  protected error = signal('');
  protected modalAbierto = signal(false);
  protected editando = signal<Paquete | null>(null);

  protected form = this.fb.group({
    nombre: ['', Validators.required],
    descripcion: ['', Validators.required],
    precio: [0, [Validators.required, Validators.min(0)]],
    caracteristicas: [''],
    destacado: [false],
  });

  ngOnInit(): void {
    this.cargar();
  }

  private cargar(): void {
    this.cargando.set(true);
    this.adminService.listarPaquetes().subscribe({
      next: (r) => {
        this.paquetes.set((r.data as any).paquetes ?? []);
        this.cargando.set(false);
      },
      error: () => this.cargando.set(false),
    });
  }

  protected abrirModal(p?: Paquete): void {
    this.editando.set(p ?? null);
    if (p) {
      this.form.patchValue({
        ...p,
        precio: parseFloat(p.precio),
        caracteristicas: p.caracteristicas?.join('\n') ?? '',
      });
    } else {
      this.form.reset({ destacado: false, precio: 0 });
    }
    this.modalAbierto.set(true);
  }

  protected cerrarModal(): void { this.modalAbierto.set(false); this.editando.set(null); this.error.set(''); }

  protected guardar(): void {
    if (this.form.invalid || this.guardando()) return;
    this.guardando.set(true);
    this.error.set('');
    const raw = this.form.value;
    const datos = {
      ...raw,
      caracteristicas: (raw.caracteristicas as string)
        .split('\n')
        .map((l) => l.trim())
        .filter(Boolean),
    };
    const p = this.editando();
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const obs = p ? this.adminService.actualizarPaquete(p.id, datos as any) : this.adminService.crearPaquete(datos as any);
    obs.subscribe({
      next: () => { this.cerrarModal(); this.cargar(); this.guardando.set(false); },
      error: (e) => { this.error.set(e?.error?.message ?? 'Error'); this.guardando.set(false); },
    });
  }

  protected eliminar(id: number): void {
    if (!confirm('¿Eliminar este paquete?')) return;
    this.adminService.eliminarPaquete(id).subscribe({ next: () => this.cargar() });
  }
}
