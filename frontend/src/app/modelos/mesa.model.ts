import { Invitado } from './invitado.model';

export interface Mesa {
  id: number;
  boda_id: number;
  numero: number;
  capacidad: number;
  created_at: string;
  updated_at: string;

  invitados?: Invitado[];
}
