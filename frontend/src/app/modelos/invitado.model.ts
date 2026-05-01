export interface Invitado {
  id: number;
  boda_id: number;
  nombre: string;
  alergias: string | null;
  acompanante: boolean;
  mesa_id: number | null;
  created_at: string;
  updated_at: string;
}
