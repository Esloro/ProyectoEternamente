<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        // Limpieza segura: la tabla pivot boda_proveedor referencia a esta,
        // asi que desactivamos FKs durante el truncate para poder re-sembrar.
        Schema::disableForeignKeyConstraints();
        DB::table('boda_proveedor')->truncate();
        Proveedor::truncate();
        Schema::enableForeignKeyConstraints();

        // Proveedores reales o realistas de la provincia de Malaga (Rincon de la
        // Victoria, Velez-Malaga, Axarquia y Malaga capital). Los precios son
        // estimaciones cerradas (no por persona) para que el calculo de
        // presupuesto sea simple (suma directa).
        $proveedores = [
            // === Lugar de celebracion (3) ===
            [
                'nombre' => 'Hacienda Nadales',
                'categoria' => 'lugar',
                'descripcion' => 'Hacienda historica en Almogia (Malaga) con jardines, capilla propia y capacidad para 250 invitados. A 25 minutos del centro de Malaga.',
                'precio' => 11500.00,
                'foto' => 'https://picsum.photos/seed/hacienda-nadales/800/600',
            ],
            [
                'nombre' => 'Cortijo Bravo',
                'categoria' => 'lugar',
                'descripcion' => 'Cortijo andaluz en Velez-Malaga rodeado de viñedos y olivares con vistas al mar. Ceremonia al aire libre y banquete en patio interior.',
                'precio' => 13500.00,
                'foto' => 'https://picsum.photos/seed/cortijo-bravo/800/600',
            ],
            [
                'nombre' => 'Restaurante El Tintero del Mar',
                'categoria' => 'lugar',
                'descripcion' => 'Restaurante en el paseo maritimo de Rincon de la Victoria para bodas intimas frente al mar. Hasta 100 invitados.',
                'precio' => 7200.00,
                'foto' => 'https://picsum.photos/seed/tintero-rincon/800/600',
            ],

            // === Floristeria (2) ===
            [
                'nombre' => 'Floristeria La Magnolia',
                'categoria' => 'floristeria',
                'descripcion' => 'Floristeria de Malaga capital especializada en bodas. Diseño con flor de temporada cultivada en la Axarquia. Incluye ramo, prendido y centros.',
                'precio' => 1900.00,
                'foto' => 'https://picsum.photos/seed/floristeria-magnolia/800/600',
            ],
            [
                'nombre' => 'Petalos del Sur',
                'categoria' => 'floristeria',
                'descripcion' => 'Estudio floral en Velez-Malaga con estilo boho mediterraneo: eucalipto, paniculata y rosas de jardin. Decoracion de altar incluida.',
                'precio' => 2400.00,
                'foto' => 'https://picsum.photos/seed/petalos-sur/800/600',
            ],

            // === Musica y DJ (2) ===
            [
                'nombre' => 'DJ Axarquia Sound',
                'categoria' => 'musica',
                'descripcion' => 'DJ local de Torre del Mar con mas de 12 años en bodas de la costa. Equipo de sonido e iluminacion incluido. 6 horas de servicio.',
                'precio' => 1300.00,
                'foto' => 'https://picsum.photos/seed/dj-axarquia/800/600',
            ],
            [
                'nombre' => 'Cuarteto Picasso',
                'categoria' => 'musica',
                'descripcion' => 'Cuarteto de cuerda de Malaga capital para ceremonia y coctel. Repertorio clasico, jazz y versiones flamencas y pop.',
                'precio' => 1500.00,
                'foto' => 'https://picsum.photos/seed/cuarteto-picasso/800/600',
            ],

            // === Fotografia y video (2) ===
            [
                'nombre' => 'Estudio Costa del Sol',
                'categoria' => 'fotografia',
                'descripcion' => 'Estudio en Rincon de la Victoria. Reportaje completo (preboda + boda) con dos fotografos. Album impreso incluido.',
                'precio' => 2300.00,
                'foto' => 'https://picsum.photos/seed/estudio-costa-sol/800/600',
            ],
            [
                'nombre' => 'Visual Andalucia',
                'categoria' => 'fotografia',
                'descripcion' => 'Productora audiovisual de Velez-Malaga. Video cinematografico 4K con dron sobre la Axarquia. Resumen de 5 minutos + pelicula completa.',
                'precio' => 1900.00,
                'foto' => 'https://picsum.photos/seed/visual-andalucia/800/600',
            ],

            // === Catering y menu (2) ===
            [
                'nombre' => 'Catering Mengual',
                'categoria' => 'catering',
                'descripcion' => 'Catering de referencia en Malaga. Menu degustacion de 9 pases con maridaje de vinos D.O. Sierras de Malaga. Servicio formal.',
                'precio' => 9800.00,
                'foto' => 'https://picsum.photos/seed/catering-mengual/800/600',
            ],
            [
                'nombre' => 'Sabores de la Axarquia',
                'categoria' => 'catering',
                'descripcion' => 'Coctel andaluz en Velez-Malaga con 25 variedades: estaciones de jamon iberico, espetos, ensaladas malagueñas y sushi mediterraneo.',
                'precio' => 7400.00,
                'foto' => 'https://picsum.photos/seed/sabores-axarquia/800/600',
            ],

            // === Decoracion (1) ===
            [
                'nombre' => 'Decoeventos Malaga',
                'categoria' => 'decoracion',
                'descripcion' => 'Estudio de decoracion en Malaga capital. Diseño integral: photocall, mesa de bienvenida, seating plan y decoracion de mesas.',
                'precio' => 1700.00,
                'foto' => 'https://picsum.photos/seed/decoeventos-malaga/800/600',
            ],

            // === Vestuario (1) ===
            [
                'nombre' => 'Atelier Mar y Sal',
                'categoria' => 'vestuario',
                'descripcion' => 'Atelier en Rincon de la Victoria. Asesoramiento personalizado de imagen para los novios y acceso a coleccion exclusiva mediterranea.',
                'precio' => 700.00,
                'foto' => 'https://picsum.photos/seed/atelier-mar-sal/800/600',
            ],

            // === Coches y transporte (1) ===
            [
                'nombre' => 'Clasicos Costa del Sol',
                'categoria' => 'transporte',
                'descripcion' => 'Coche de epoca con chofer en Malaga (Mercedes 280 SE de 1972). 4 horas de servicio entre la iglesia y la finca.',
                'precio' => 900.00,
                'foto' => 'https://picsum.photos/seed/clasicos-costa-sol/800/600',
            ],

            // === Detalles para invitados (1) ===
            [
                'nombre' => 'Detalles Mediterraneos',
                'categoria' => 'detalles',
                'descripcion' => 'Recordatorios artesanos de la Axarquia: aceite virgen extra de Periana o miel de caña de Frigiliana en frasco con etiqueta personalizada.',
                'precio' => 700.00,
                'foto' => 'https://picsum.photos/seed/detalles-mediterraneos/800/600',
            ],

            // === Tarta nupcial (1) ===
            [
                'nombre' => 'Pasteleria La Canela',
                'categoria' => 'tarta',
                'descripcion' => 'Pasteleria artesana de Velez-Malaga. Tarta de 3 pisos con buttercream y flores naturales. Sabores a elegir (red velvet, limon, chocolate).',
                'precio' => 420.00,
                'foto' => 'https://picsum.photos/seed/pasteleria-canela/800/600',
            ],
        ];

        foreach ($proveedores as $proveedor) {
            Proveedor::create($proveedor);
        }
    }
}
