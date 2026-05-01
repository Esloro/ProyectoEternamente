export interface MensajeContacto {
  id: number;
  nombre: string;
  email: string;
  telefono: string | null;
  mensaje: string;
  leido: boolean;
  created_at: string;
  updated_at: string;
}

export interface DatosFormularioContacto {
  nombre: string;
  email: string;
  telefono?: string | null;
  mensaje: string;
}
