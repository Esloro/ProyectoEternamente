import { Component, OnInit, inject, signal } from '@angular/core';
import { DecimalPipe } from '@angular/common';

import { BodaService } from '../../../servicios/boda.service';
import { ProveedorService } from '../../../servicios/proveedor.service';
import {
  CategoriaConProveedores,
  ETIQUETAS_CATEGORIA,
  Proveedor,
} from '../../../modelos';

@Component({
  selector: 'app-personalizacion',
  standalone: true,
  imports: [DecimalPipe],
  templateUrl: './personalizacion.html',
  styleUrl: './personalizacion.scss',
})
export class Personalizacion implements OnInit {
  private bodaService = inject(BodaService);
  private proveedorService = inject(ProveedorService);

  protected categorias = signal<CategoriaConProveedores[]>([]);
  protected seleccionados = signal<Set<number>>(new Set());
  protected cargando = signal(true);
  protected guardando = signal(false);
  protected exito = signal('');
  protected error = signal('');
  protected categoriaActiva = signal('');

  protected readonly etiquetasCategoria = ETIQUETAS_CATEGORIA;

  ngOnInit(): void {
    Promise.all([
      this.proveedorService.todos().toPromise(),
      this.bodaService.misProveedores().toPromise(),
    ]).then(([provRes, bodaRes]) => {
      if (provRes?.data.categorias) {
        const cats = provRes.data.categorias;
        this.categorias.set(cats);
        if (cats.length > 0) this.categoriaActiva.set(cats[0].categoria);
      }
      if (bodaRes?.data.proveedores) {
        const ids = new Set(bodaRes.data.proveedores.map((p: Proveedor) => p.id));
        this.seleccionados.set(ids);
      }
      this.cargando.set(false);
    }).catch(() => this.cargando.set(false));
  }

  protected get categoriaActivaData(): CategoriaConProveedores | undefined {
    return this.categorias().find((c) => c.categoria === this.categoriaActiva());
  }

  protected toggleProveedor(proveedor: Proveedor): void {
    const ids = new Set(this.seleccionados());
    if (ids.has(proveedor.id)) {
      ids.delete(proveedor.id);
    } else {
      // Solo uno por categoría
      const cat = this.categorias().find((c) => c.categoria === this.categoriaActiva());
      if (cat) {
        for (const p of cat.proveedores) ids.delete(p.id);
      }
      ids.add(proveedor.id);
    }
    this.seleccionados.set(ids);
  }

  protected estaSeleccionado(id: number): boolean {
    return this.seleccionados().has(id);
  }

  protected tieneSeleccionEnCategoria(cat: CategoriaConProveedores): boolean {
    return cat.proveedores.some((p) => this.seleccionados().has(p.id));
  }

  protected guardar(): void {
    if (this.guardando()) return;
    this.guardando.set(true);
    this.exito.set('');
    this.error.set('');

    const proveedores = Array.from(this.seleccionados()).map((id) => ({ id }));

    this.bodaService.guardarProveedores(proveedores).subscribe({
      next: () => {
        this.exito.set('Selección guardada correctamente.');
        this.guardando.set(false);
        setTimeout(() => this.exito.set(''), 3000);
      },
      error: (e) => {
        this.error.set(e?.error?.message ?? 'Error al guardar. Inténtalo de nuevo.');
        this.guardando.set(false);
      },
    });
  }
}
