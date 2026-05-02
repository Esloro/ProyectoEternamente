export interface Invitado {
  id: number;
  boda_id: number;
  nombre: string;
  alergias: string | null;
  num_acompanantes: number;
  mesa_id: number | null;
  created_at: string;
  updated_at: string;
}
