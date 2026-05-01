<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Orden importante: los seeders posteriores referencian los primeros.
        $this->call([
            UsuarioSeeder::class,
            ProveedorSeeder::class,
            PaqueteSeeder::class,
            PortfolioSeeder::class,
            TestimonioSeeder::class,
            BodaSeeder::class, // requiere usuarios y proveedores
        ]);
    }
}
