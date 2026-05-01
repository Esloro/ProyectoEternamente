import { Component, OnInit, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';

import { AdminService } from '../../../../servicios/admin.service';
import { Portfolio } from '../../../../modelos';

@Component({
  selector: 'app-portfolio-admin',
  standalone: true,
  imports: [ReactiveFormsModule],
  templateUrl: './portfolio.html',
  styleUrl: './portfolio.scss',
})
export class PortfolioAdmin implements OnInit {
  private adminService = inject(AdminService);
  private fb = inject(FormBuilder);

  protected items = signal<Portfolio[]>([]);
  protected cargando = signal(true);
  protected guardando = signal(false);
  protected error = signal('');
  protected modalAbierto = signal(false);
  protected editando = signal<Portfolio | null>(null);

  protected form = this.fb.group({
    titulo: ['', Validators.required],
    descripcion: [''],
    foto: ['', Validators.required],
    orden: [0, Validators.required],
  });

  ngOnInit(): void {
    this.cargar();
  }

  private cargar(): void {
    this.cargando.set(true);
    this.adminService.listarPortfolio().subscribe({
      next: (r) => { this.items.set((r.data as any).portfolio ?? []); this.cargando.set(false); },
      error: () => this.cargando.set(false),
    });
  }

  protected abrirModal(p?: Portfolio): void {
    this.editando.set(p ?? null);
    p ? this.form.patchValue(p) : this.form.reset({ orden: this.items().length + 1 });
    this.modalAbierto.set(true);
  }

  protected cerrarModal(): void { this.modalAbierto.set(false); this.editando.set(null); this.error.set(''); }

  protected guardar(): void {
    if (this.form.invalid || this.guardando()) return;
    this.guardando.set(true);
    this.error.set('');
    const datos = this.form.value;
    const p = this.editando();
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const obs = p ? this.adminService.actualizarPortfolio(p.id, datos as any) : this.adminService.crearPortfolio(datos as any);
    obs.subscribe({
      next: () => { this.cerrarModal(); this.cargar(); this.guardando.set(false); },
      error: (e) => { this.error.set(e?.error?.message ?? 'Error'); this.guardando.set(false); },
    });
  }

  protected eliminar(id: number): void {
    if (!confirm('¿Eliminar este elemento?')) return;
    this.adminService.eliminarPortfolio(id).subscribe({ next: () => this.cargar() });
  }
}
