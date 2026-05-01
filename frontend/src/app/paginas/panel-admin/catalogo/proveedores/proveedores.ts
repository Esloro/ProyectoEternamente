import { Component, OnInit, inject, signal } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';

import { AdminService } from '../../../../servicios/admin.service';
import {
  CategoriaProveedor,
  ETIQUETAS_CATEGORIA,
  Proveedor,
} from '../../../../modelos';

@Component({
  selector: 'app-proveedores-admin',
  standalone: true,
  imports: [DecimalPipe, ReactiveFormsModule],
  templateUrl: './proveedores.html',
  styleUrl: './proveedores.scss',
})
export class ProveedoresAdmin implements OnInit {
  private adminService = inject(AdminService);
  private fb = inject(FormBuilder);

  protected proveedores = signal<Proveedor[]>([]);
  protected cargando = signal(true);
  protected guardando = signal(false);
  protected error = signal('');
  protected modalAbierto = signal(false);
  protected editando = signal<Proveedor | null>(null);

  protected readonly etiquetasCategoria = ETIQUETAS_CATEGORIA;
  protected readonly categorias = Object.keys(ETIQUETAS_CATEGORIA) as CategoriaProveedor[];

  protected form = this.fb.group({
    nombre: ['', Validators.required],
    categoria: ['lugar' as CategoriaProveedor, Validators.required],
    descripcion: [''],
    precio: [0, [Validators.required, Validators.min(0)]],
    foto: [''],
    activo: [true],
  });

  ngOnInit(): void {
    this.cargar();
  }

  private cargar(): void {
    this.cargando.set(true);
    this.adminService.listarProveedores().subscribe({
      next: (r) => { this.proveedores.set(r.data.data ?? []); this.cargando.set(false); },
      error: () => this.cargando.set(false),
    });
  }

  protected abrirModal(prov?: Proveedor): void {
    this.editando.set(prov ?? null);
    if (prov) {
      this.form.patchValue({ ...prov, precio: parseFloat(prov.precio) });
    } else {
      this.form.reset({ categoria: 'lugar', activo: true, precio: 0 });
    }
    this.modalAbierto.set(true);
  }

  protected cerrarModal(): void {
    this.modalAbierto.set(false);
    this.editando.set(null);
    this.error.set('');
  }

  protected guardar(): void {
    if (this.form.invalid || this.guardando()) return;
    this.guardando.set(true);
    this.error.set('');
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const datos = this.form.value as any;
    const prov = this.editando();
    const obs = prov
      ? this.adminService.actualizarProveedor(prov.id, datos)
      : this.adminService.crearProveedor(datos);
    obs.subscribe({
      next: () => { this.cerrarModal(); this.cargar(); this.guardando.set(false); },
      error: (e) => { this.error.set(e?.error?.message ?? 'Error'); this.guardando.set(false); },
    });
  }

  protected eliminar(id: number): void {
    if (!confirm('¿Eliminar este proveedor?')) return;
    this.adminService.eliminarProveedor(id).subscribe({ next: () => this.cargar() });
  }
}
