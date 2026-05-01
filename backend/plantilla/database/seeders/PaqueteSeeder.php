<?php

namespace Database\Seeders;

use App\Models\Paquete;
use Illuminate\Database\Seeder;

class PaqueteSeeder extends Seeder
{
    public function run(): void
    {
        Paquete::create([
            'nombre' => 'Solo Asesoramiento',
            'descripcion' => 'Te acompañamos durante toda la planificacion con consejos y proveedores recomendados. La coordinacion del dia corre por tu cuenta.',
            'precio' => 1200.00,
            'caracteristicas' => [
                'Hasta 5 reuniones de planificacion',
                'Seleccion personalizada de proveedores',
                'Plantillas de planificacion y checklist',
                'Soporte por email y telefono',
            ],
            'destacado' => false,
        ]);

        Paquete::create([
            'nombre' => 'Paquete Base',
            'descripcion' => 'Planificacion completa con coordinacion el dia de la boda. La opcion mas elegida por nuestras parejas.',
            'precio' => 3500.00,
            'caracteristicas' => [
                'Planificacion integral (12+ reuniones)',
                'Gestion de todos los proveedores',
                'Coordinacion in situ el dia de la boda',
                'Seating plan y organizacion de mesas',
                'Asesoramiento de imagen y protocolo',
            ],
            'destacado' => true,
        ]);

        Paquete::create([
            'nombre' => 'Paquete Premium',
            'descripcion' => 'Servicio integral de lujo con detalles exclusivos y atencion 24/7 durante los meses previos.',
            'precio' => 6500.00,
            'caracteristicas' => [
                'Todo lo incluido en el Paquete Base',
                'Equipo dedicado de 3 personas el dia de la boda',
                'Diseño exclusivo de papeleria e invitaciones',
                'Coordinacion de luna de miel',
                'Atencion 24/7 durante los 6 meses previos',
                'Servicio de canguro para invitados con niños',
            ],
            'destacado' => false,
        ]);
    }
}
