export interface Paquete {
  id: number;
  nombre: string;
  descripcion: string;
  precio: string;
  caracteristicas: string[] | null;
  destacado: boolean;
  created_at: string;
  updated_at: string;
}
