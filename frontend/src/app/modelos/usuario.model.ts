export type Rol = 'cliente' | 'administrador';

export interface Usuario {
  id: number;
  nombre: string;
  apellidos: string;
  email: string;
  telefono: string | null;
  rol: Rol;
  email_verificado_en: string | null;
  created_at: string;
  updated_at: string;
}

export interface DatosRegistro {
  nombre: string;
  apellidos: string;
  email: string;
  telefono?: string | null;
  password: string;
  password_confirmation: string;
}

export interface DatosLogin {
  email: string;
  password: string;
}
