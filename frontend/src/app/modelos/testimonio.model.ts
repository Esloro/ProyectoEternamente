export interface Testimonio {
  id: number;
  nombre_cliente: string;
  foto: string | null;
  valoracion: number;       // 1 a 5
  comentario: string;
  verificado: boolean;
  created_at: string;
  updated_at: string;
}
