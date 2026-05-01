import { Usuario } from './usuario.model';

export interface Mensaje {
  id: number;
  boda_id: number;
  emisor_id: number;
  receptor_id: number;
  contenido: string;
  leido: boolean;
  created_at: string;
  updated_at: string;

  emisor?: Usuario;
  receptor?: Usuario;
}
