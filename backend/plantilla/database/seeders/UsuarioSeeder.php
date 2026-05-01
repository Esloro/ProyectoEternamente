<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        // 1 administrador (no se crea por registro público).
        Usuario::create([
            'nombre' => 'Admin',
            'apellidos' => 'Wedding Planner',
            'email' => 'admin@weddingplanner.com',
            'telefono' => '600000000',
            'password' => Hash::make('admin1234'),
            'rol' => 'administrador',
            'email_verificado_en' => now(),
        ]);

        // Cliente con boda PENDIENTE_REUNION (rellenó el cuestionario,
        // todavía no ha tenido la reunión inicial con el admin).
        Usuario::create([
            'nombre' => 'Lucia',
            'apellidos' => 'Garcia Lopez',
            'email' => 'lucia@example.com',
            'telefono' => '611111111',
            'password' => Hash::make('cliente1234'),
            'rol' => 'cliente',
            'email_verificado_en' => now(),
        ]);

        // Cliente con boda ACTIVA (ya contratada, panel completo desbloqueado).
        Usuario::create([
            'nombre' => 'Carlos',
            'apellidos' => 'Martin Ruiz',
            'email' => 'carlos@example.com',
            'telefono' => '622222222',
            'password' => Hash::make('cliente1234'),
            'rol' => 'cliente',
            'email_verificado_en' => now(),
        ]);
    }
}
