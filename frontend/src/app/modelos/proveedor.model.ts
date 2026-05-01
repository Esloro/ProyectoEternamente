export type CategoriaProveedor =
  | 'lugar'
  | 'floristeria'
  | 'musica'
  | 'fotografia'
  | 'catering'
  | 'decoracion'
  | 'vestuario'
  | 'transporte'
  | 'detalles'
  | 'tarta';

export interface Proveedor {
  id: number;
  nombre: string;
  categoria: CategoriaProveedor;
  descripcion: string | null;
  foto: string | null;
  precio: string;        // decimal serializado como string
  activo: boolean;
  created_at: string;
  updated_at: string;

  // pivot cuando viene a traves de boda.proveedores
  pivot?: {
    boda_id: number;
    proveedor_id: number;
    notas: string | null;
  };
}

export interface CategoriaConProveedores {
  categoria: CategoriaProveedor;
  etiqueta: string;
  proveedores: Proveedor[];
}

export const ETIQUETAS_CATEGORIA: Record<CategoriaProveedor, string> = {
  lugar: 'Lugar de celebración',
  floristeria: 'Floristería',
  musica: 'Música y DJ',
  fotografia: 'Fotografía y vídeo',
  catering: 'Catering y menú',
  decoracion: 'Decoración y temática',
  vestuario: 'Vestuario y trajes',
  transporte: 'Coches y transporte',
  detalles: 'Detalles para invitados',
  tarta: 'Tarta nupcial',
};
