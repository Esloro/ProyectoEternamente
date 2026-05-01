import { Component, OnInit, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';

import { AdminService } from '../../../../servicios/admin.service';
import { Testimonio } from '../../../../modelos';

@Component({
  selector: 'app-testimonios-admin',
  standalone: true,
  imports: [ReactiveFormsModule],
  templateUrl: './testimonios.html',
  styleUrl: './testimonios.scss',
})
export class TestimoniosAdmin implements OnInit {
  private adminService = inject(AdminService);
  private fb = inject(FormBuilder);

  protected testimonios = signal<Testimonio[]>([]);
  protected cargando = signal(true);
  protected guardando = signal(false);
  protected error = signal('');
  protected modalAbierto = signal(false);
  protected editando = signal<Testimonio | null>(null);

  protected form = this.fb.group({
    nombre_cliente: ['', Validators.required],
    comentario: ['', Validators.required],
    valoracion: [5, [Validators.required, Validators.min(1), Validators.max(5)]],
    foto: [''],
    verificado: [true],
  });

  ngOnInit(): void {
    this.cargar();
  }

  private cargar(): void {
    this.cargando.set(true);
    this.adminService.listarTestimonios().subscribe({
      next: (r) => { this.testimonios.set((r.data as any).testimonios ?? []); this.cargando.set(false); },
      error: () => this.cargando.set(false),
    });
  }

  protected abrirModal(t?: Testimonio): void {
    this.editando.set(t ?? null);
    t ? this.form.patchValue(t) : this.form.reset({ valoracion: 5, verificado: true });
    this.modalAbierto.set(true);
  }

  protected cerrarModal(): void { this.modalAbierto.set(false); this.editando.set(null); this.error.set(''); }

  protected guardar(): void {
    if (this.form.invalid || this.guardando()) return;
    this.guardando.set(true);
    this.error.set('');
    const datos = this.form.value;
    const t = this.editando();
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const obs = t ? this.adminService.actualizarTestimonio(t.id, datos as any) : this.adminService.crearTestimonio(datos as any);
    obs.subscribe({
      next: () => { this.cerrarModal(); this.cargar(); this.guardando.set(false); },
      error: (e) => { this.error.set(e?.error?.message ?? 'Error'); this.guardando.set(false); },
    });
  }

  protected eliminar(id: number): void {
    if (!confirm('¿Eliminar este testimonio?')) return;
    this.adminService.eliminarTestimonio(id).subscribe({ next: () => this.cargar() });
  }

  protected estrellas(n: number): string {
    return '★'.repeat(n) + '☆'.repeat(5 - n);
  }
}
