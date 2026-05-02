<?php

namespace Database\Seeders;

use App\Models\Boda;
use App\Models\Invitado;
use App\Models\Mensaje;
use App\Models\Mesa;
use App\Models\Proveedor;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class BodaSeeder extends Seeder
{
    public function run(): void
    {
        $admin  = Usuario::where('email', 'admin@weddingplanner.com')->firstOrFail();
        $lucia  = Usuario::where('email', 'lucia@example.com')->firstOrFail();
        $carlos = Usuario::where('email', 'carlos@example.com')->firstOrFail();

        // -------------------------------------------------------------
        // Boda 1: Lucia -- estado PENDIENTE_REUNION
        // (Acaba de rellenar el cuestionario, no tiene panel desbloqueado)
        // -------------------------------------------------------------
        Boda::create([
            'usuario_id' => $lucia->id,
            'nombre_pareja' => 'Lucia y David',
            'tipo_ceremonia' => 'religiosa',
            'lugar_celebracion' => 'iglesia',
            'fecha_boda' => '2026-09-15',
            'num_invitados' => 120,
            'franja_horaria' => 'tarde',
            'tematica' => 'clasica',
            'tipo_comida' => 'banquete',
            'presupuesto_orientativo' => '20000_35000',
            'estado' => Boda::ESTADO_PENDIENTE,
            'presupuesto_estimado' => 0,
        ]);

        // -------------------------------------------------------------
        // Boda 2: Carlos -- estado ACTIVA con datos completos
        // (Panel desbloqueado, ya ha elegido proveedores y mesas)
        // -------------------------------------------------------------
        $bodaCarlos = Boda::create([
            'usuario_id' => $carlos->id,
            'nombre_pareja' => 'Carlos y Marta',
            'tipo_ceremonia' => 'simbolica',
            'lugar_celebracion' => 'finca',
            'fecha_boda' => '2026-07-10',
            'num_invitados' => 80,
            'franja_horaria' => 'tarde',
            'tematica' => 'rustica',
            'tipo_comida' => 'coctel',
            'presupuesto_orientativo' => '10000_20000',
            'estado' => Boda::ESTADO_ACTIVA,
        ]);

        // Carlos elige un proveedor por categoria de las mas habituales.
        $categoriasElegidas = ['lugar', 'musica', 'fotografia', 'catering', 'tarta', 'transporte'];
        $proveedoresIds = collect($categoriasElegidas)
            ->map(fn ($cat) => Proveedor::where('categoria', $cat)->first()?->id)
            ->filter()
            ->all();

        $bodaCarlos->proveedores()->attach($proveedoresIds);
        $bodaCarlos->recalcularPresupuesto();

        // Mesas para la boda de Carlos (10 mesas de 8 plazas = 80 invitados).
        $mesa1 = Mesa::create(['boda_id' => $bodaCarlos->id, 'numero' => 1, 'capacidad' => 8]);
        $mesa2 = Mesa::create(['boda_id' => $bodaCarlos->id, 'numero' => 2, 'capacidad' => 8]);
        $mesa3 = Mesa::create(['boda_id' => $bodaCarlos->id, 'numero' => 3, 'capacidad' => 8]);

        // Algunos invitados de prueba (los demas se añadiran desde el panel).
        Invitado::create(['boda_id' => $bodaCarlos->id, 'nombre' => 'Maria Lopez',     'mesa_id' => $mesa1->id]);
        Invitado::create(['boda_id' => $bodaCarlos->id, 'nombre' => 'Juan Perez',      'alergias' => 'Frutos secos', 'mesa_id' => $mesa1->id]);
        Invitado::create(['boda_id' => $bodaCarlos->id, 'nombre' => 'Ana Garcia',      'num_acompanantes' => 1, 'mesa_id' => $mesa2->id]);
        Invitado::create(['boda_id' => $bodaCarlos->id, 'nombre' => 'Pedro Martinez',  'mesa_id' => $mesa2->id]);
        Invitado::create(['boda_id' => $bodaCarlos->id, 'nombre' => 'Sofia Ramirez',   'alergias' => 'Lactosa', 'mesa_id' => $mesa3->id]);
        Invitado::create(['boda_id' => $bodaCarlos->id, 'nombre' => 'Diego Hernandez']); // sin mesa asignada

        // Conversacion de chat entre Carlos y el admin.
        Mensaje::create([
            'boda_id' => $bodaCarlos->id,
            'emisor_id' => $admin->id,
            'receptor_id' => $carlos->id,
            'contenido' => '¡Hola Carlos! Nos hemos puesto en marcha con tu boda. ¿Te paso esta semana las opciones de finca al aire libre que comentamos?',
            'leido' => true,
        ]);

        Mensaje::create([
            'boda_id' => $bodaCarlos->id,
            'emisor_id' => $carlos->id,
            'receptor_id' => $admin->id,
            'contenido' => 'Si, perfecto. ¿Podriais enviarme tambien el contrato del fotografo para firmarlo?',
            'leido' => false,
        ]);
    }
}
