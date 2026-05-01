import { Proveedor } from './proveedor.model';
import { Mesa } from './mesa.model';
import { Invitado } from './invitado.model';

export type EstadoBoda = 'pendiente_reunion' | 'activa' | 'finalizada' | 'cancelada';
export type TipoCeremonia = 'civil' | 'iglesia' | 'aire_libre' | 'otra';
export type FranjaHoraria = 'manana' | 'tarde' | 'noche';
export type Tematica = 'clasica' | 'rustica' | 'moderna' | 'boho' | 'glamour';
export type TipoComida = 'coctel' | 'banquete' | 'buffet' | 'familiar';
export type PresupuestoOrientativo = 'hasta_10000' | '10000_20000' | '20000_35000' | '35000_50000' | 'mas_50000';

export interface Boda {
  id: number;
  usuario_id: number;
  tipo_ceremonia: TipoCeremonia;
  iglesia: string | null;
  fecha_boda: string;       // ISO
  num_invitados: number;
  franja_horaria: FranjaHoraria;
  tematica: Tematica;
  tipo_comida: TipoComida;
  presupuesto_orientativo: PresupuestoOrientativo;
  estado: EstadoBoda;
  presupuesto_estimado: string;          // decimal serializado como string
  presupuesto_definitivo: string | null;
  created_at: string;
  updated_at: string;

  // relaciones opcionales (se cargan con `with` en el backend)
  proveedores?: Proveedor[];
  mesas?: Mesa[];
  invitados?: Invitado[];
}

/**
 * Datos del cuestionario inicial (post-registro). Mismos campos que la
 * tabla `bodas` excepto los gestionados por el sistema.
 */
export interface DatosCuestionarioInicial {
  tipo_ceremonia: TipoCeremonia;
  iglesia?: string | null;
  fecha_boda: string;
  num_invitados: number;
  franja_horaria: FranjaHoraria;
  tematica: Tematica;
  tipo_comida: TipoComida;
  presupuesto_orientativo: PresupuestoOrientativo;
}

// Etiquetas legibles para mostrar en la UI (los valores ENUM no llevan tildes).
export const ETIQUETAS_FRANJA: Record<FranjaHoraria, string> = {
  manana: 'Mañana',
  tarde: 'Tarde',
  noche: 'Noche',
};

export const ETIQUETAS_CEREMONIA: Record<TipoCeremonia, string> = {
  civil: 'Civil',
  iglesia: 'Iglesia',
  aire_libre: 'Al aire libre',
  otra: 'Otra',
};

export const ETIQUETAS_TEMATICA: Record<Tematica, string> = {
  clasica: 'Clásica',
  rustica: 'Rústica',
  moderna: 'Moderna',
  boho: 'Boho',
  glamour: 'Glamour',
};

export const ETIQUETAS_COMIDA: Record<TipoComida, string> = {
  coctel: 'Cóctel',
  banquete: 'Banquete',
  buffet: 'Buffet',
  familiar: 'Familiar',
};

export const ETIQUETAS_PRESUPUESTO: Record<PresupuestoOrientativo, string> = {
  hasta_10000: 'Hasta 10.000 €',
  '10000_20000': '10.000 € – 20.000 €',
  '20000_35000': '20.000 € – 35.000 €',
  '35000_50000': '35.000 € – 50.000 €',
  mas_50000: 'Más de 50.000 €',
};

export const ETIQUETAS_ESTADO: Record<EstadoBoda, string> = {
  pendiente_reunion: 'Pendiente de reunión',
  activa: 'Activa',
  finalizada: 'Finalizada',
  cancelada: 'Cancelada',
};
