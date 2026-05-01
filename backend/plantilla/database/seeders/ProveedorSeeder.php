<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        // 16 proveedores cubriendo las 10 categorías. Los precios son
        // estimaciones cerradas (no por persona) para que el cálculo de
        // presupuesto sea simple (suma directa). Las fotos usan picsum.photos
        // como placeholder; en producción se sustituirían por fotos reales.
        $proveedores = [
            // === Lugar de celebración (3) ===
            [
                'nombre' => 'Finca El Romeral',
                'categoria' => 'lugar',
                'descripcion' => 'Finca rural a 30 min de Madrid con jardines, capilla propia y capacidad para 200 invitados.',
                'precio' => 9500.00,
                'foto' => 'https://picsum.photos/seed/finca-romeral/800/600',
            ],
            [
                'nombre' => 'Hacienda Los Olivos',
                'categoria' => 'lugar',
                'descripcion' => 'Cortijo andaluz centenario con olivos milenarios. Ceremonia al aire libre y banquete en patio interior.',
                'precio' => 12500.00,
                'foto' => 'https://picsum.photos/seed/hacienda-olivos/800/600',
            ],
            [
                'nombre' => 'Restaurante Mar y Tierra',
                'categoria' => 'lugar',
                'descripcion' => 'Restaurante con vistas al mar para bodas intimas de hasta 80 personas.',
                'precio' => 6500.00,
                'foto' => 'https://picsum.photos/seed/mar-tierra/800/600',
            ],

            // === Floristeria (2) ===
            [
                'nombre' => 'Floristeria Aurora',
                'categoria' => 'floristeria',
                'descripcion' => 'Diseño floral elegante con flor de temporada. Incluye ramo de novia, prendido y centros de mesa.',
                'precio' => 1800.00,
                'foto' => 'https://picsum.photos/seed/floristeria-aurora/800/600',
            ],
            [
                'nombre' => 'Petalos Salvajes',
                'categoria' => 'floristeria',
                'descripcion' => 'Estilo boho y silvestre con eucalipto, peonias y rosas de jardin.',
                'precio' => 2400.00,
                'foto' => 'https://picsum.photos/seed/petalos-salvajes/800/600',
            ],

            // === Musica y DJ (2) ===
            [
                'nombre' => 'DJ Marco Sonido',
                'categoria' => 'musica',
                'descripcion' => 'DJ con mas de 10 años de experiencia. Equipo de sonido e iluminacion incluido. 6 horas de servicio.',
                'precio' => 1200.00,
                'foto' => 'https://picsum.photos/seed/dj-marco/800/600',
            ],
            [
                'nombre' => 'Cuarteto Vivace',
                'categoria' => 'musica',
                'descripcion' => 'Cuarteto de cuerda para ceremonia y coctel. Repertorio clasico, jazz y versiones pop.',
                'precio' => 1500.00,
                'foto' => 'https://picsum.photos/seed/cuarteto-vivace/800/600',
            ],

            // === Fotografia y video (2) ===
            [
                'nombre' => 'Estudio Luz Eterna',
                'categoria' => 'fotografia',
                'descripcion' => 'Reportaje completo (preboda + boda) con dos fotografos. Album impreso incluido.',
                'precio' => 2200.00,
                'foto' => 'https://picsum.photos/seed/luz-eterna/800/600',
            ],
            [
                'nombre' => 'Memorias en Movimiento',
                'categoria' => 'fotografia',
                'descripcion' => 'Video cinematografico 4K con dron. Resumen de 5 minutos + pelicula completa de la boda.',
                'precio' => 1800.00,
                'foto' => 'https://picsum.photos/seed/memorias-movimiento/800/600',
            ],

            // === Catering y menu (2) ===
            [
                'nombre' => 'Catering Gourmet Plus',
                'categoria' => 'catering',
                'descripcion' => 'Menu degustacion de 9 pases con maridaje de vinos D.O. Pensado para banquete formal.',
                'precio' => 9500.00,
                'foto' => 'https://picsum.photos/seed/gourmet-plus/800/600',
            ],
            [
                'nombre' => 'Sabores del Mundo',
                'categoria' => 'catering',
                'descripcion' => 'Coctel con 25 variedades y estaciones de jamon iberico, sushi y dim sum.',
                'precio' => 7200.00,
                'foto' => 'https://picsum.photos/seed/sabores-mundo/800/600',
            ],

            // === Decoracion (1) ===
            [
                'nombre' => 'Decora Tu Boda',
                'categoria' => 'decoracion',
                'descripcion' => 'Diseño integral: photocall, mesa de bienvenida, seating plan y decoracion de mesas.',
                'precio' => 1600.00,
                'foto' => 'https://picsum.photos/seed/decora-boda/800/600',
            ],

            // === Vestuario (1) ===
            [
                'nombre' => 'Atelier Novia',
                'categoria' => 'vestuario',
                'descripcion' => 'Asesoramiento personalizado de imagen para los novios. Acceso a coleccion exclusiva.',
                'precio' => 600.00,
                'foto' => 'https://picsum.photos/seed/atelier-novia/800/600',
            ],

            // === Coches y transporte (1) ===
            [
                'nombre' => 'Bodas en Clasicos',
                'categoria' => 'transporte',
                'descripcion' => 'Coche de epoca con chofer (Mercedes 280 SE de 1972). 4 horas de servicio.',
                'precio' => 850.00,
                'foto' => 'https://picsum.photos/seed/bodas-clasicos/800/600',
            ],

            // === Detalles para invitados (1) ===
            [
                'nombre' => 'Detalles con Cariño',
                'categoria' => 'detalles',
                'descripcion' => 'Recordatorios personalizados para los invitados: aceite gourmet o miel artesana en frasco con etiqueta.',
                'precio' => 650.00,
                'foto' => 'https://picsum.photos/seed/detalles-cariño/800/600',
            ],

            // === Tarta nupcial (1) ===
            [
                'nombre' => 'Pasteleria Dulce Boda',
                'categoria' => 'tarta',
                'descripcion' => 'Tarta de 3 pisos con buttercream y flores naturales. Sabores a elegir (red velvet, limon, chocolate).',
                'precio' => 380.00,
                'foto' => 'https://picsum.photos/seed/dulce-boda/800/600',
            ],
        ];

        foreach ($proveedores as $proveedor) {
            Proveedor::create($proveedor);
        }
    }
}
