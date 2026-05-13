<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        // IMPORTANTE: este seeder es IDEMPOTENTE. Usa firstOrCreate por nombre
        // para no borrar los proveedores que el admin haya creado manualmente
        // desde el panel. Si ya existe uno con el mismo nombre, no se toca.
        // Si quieres regenerar todo desde cero, usa `migrate:fresh --seed`.

        // Las fotos viven en frontend/public/bodas/ y las sirve Vercel.
        // Guardamos rutas relativas para que el <img src> las resuelva contra
        // el dominio del frontend, sin depender de APP_URL del backend.
        $base = '/bodas';

        // Proveedores reales o realistas de la provincia de Malaga (Rincon de la
        // Victoria, Velez-Malaga, Axarquia y Malaga capital). Los precios son
        // estimaciones cerradas (no por persona) para que el calculo de
        // presupuesto sea simple (suma directa).
        $proveedores = [
            // === Lugar de celebracion (5) ===
            [
                'nombre' => 'Hacienda Nadales',
                'categoria' => 'lugar',
                'descripcion' => 'Hacienda historica en Almogia (Malaga) con jardines, capilla propia y capacidad para 250 invitados. A 25 minutos del centro de Malaga.',
                'precio' => 11500.00,
                'foto' => "$base/HaciendaNadalesjpg.jpg",
            ],
            [
                'nombre' => 'Cortijo Bravo',
                'categoria' => 'lugar',
                'descripcion' => 'Cortijo andaluz en Velez-Malaga rodeado de viñedos y olivares con vistas al mar. Ceremonia al aire libre y banquete en patio interior.',
                'precio' => 13500.00,
                'foto' => "$base/CortijoBravo.jpg",
            ],
            [
                'nombre' => 'Finca La Concepcion',
                'categoria' => 'lugar',
                'descripcion' => 'Jardin botanico historico de Malaga con arboles centenarios y elementos arquitectonicos unicos. Ceremonia y coctel al aire libre.',
                'precio' => 14200.00,
                'foto' => "$base/fincaConcepcionjpg.jpg",
            ],
            [
                'nombre' => 'Hacienda La Solea',
                'categoria' => 'lugar',
                'descripcion' => 'Finca andaluza en la Axarquia con patio interior, capilla y zona de banquete cubierta. Hasta 200 invitados con alojamiento para los novios.',
                'precio' => 10800.00,
                'foto' => "$base/hacienda1.jpg",
            ],
            [
                'nombre' => 'Restaurante El Tintero del Mar',
                'categoria' => 'lugar',
                'descripcion' => 'Restaurante en el paseo maritimo de Rincon de la Victoria para bodas intimas frente al mar. Hasta 100 invitados.',
                'precio' => 7200.00,
                'foto' => "$base/altar.jpg",
            ],

            // === Floristeria (5) ===
            [
                'nombre' => 'Floristeria La Magnolia',
                'categoria' => 'floristeria',
                'descripcion' => 'Floristeria de Malaga capital especializada en bodas. Diseño con flor de temporada cultivada en la Axarquia. Incluye ramo, prendido y centros.',
                'precio' => 1900.00,
                'foto' => "$base/laMagnolia.jpeg",
            ],
            [
                'nombre' => 'Petalos del Sur',
                'categoria' => 'floristeria',
                'descripcion' => 'Estudio floral en Velez-Malaga con estilo boho mediterraneo: eucalipto, paniculata y rosas de jardin. Decoracion de altar incluida.',
                'precio' => 2400.00,
                'foto' => "$base/FloresMagnolia.jpg",
            ],
            [
                'nombre' => 'Arte Floral Malaga',
                'categoria' => 'floristeria',
                'descripcion' => 'Estudio en Malaga capital con disenos contemporaneos. Composiciones asimetricas con peonias, hortensias y vegetacion mediterranea.',
                'precio' => 2100.00,
                'foto' => "$base/FloresArteFloral.jpeg",
            ],
            [
                'nombre' => 'Agapanthus Eventos',
                'categoria' => 'floristeria',
                'descripcion' => 'Floristeria de eventos en Torre del Mar. Especialistas en arcos florales para ceremonias y caminos de mesa con flor seca y fresca.',
                'precio' => 2600.00,
                'foto' => "$base/Agapanthus.jpg",
            ],
            [
                'nombre' => 'Centros y Ramos Mediterraneo',
                'categoria' => 'floristeria',
                'descripcion' => 'Pequeño taller artesano en Nerja. Centros de mesa con flores de la zona y ramos clasicos de novia con cinta de seda natural.',
                'precio' => 1500.00,
                'foto' => "$base/centroFlores.jpg",
            ],

            // === Musica y DJ (2) ===
            [
                'nombre' => 'DJ Axarquia Sound',
                'categoria' => 'musica',
                'descripcion' => 'DJ local de Torre del Mar con mas de 12 años en bodas de la costa. Equipo de sonido e iluminacion incluido. 6 horas de servicio.',
                'precio' => 1300.00,
                'foto' => "$base/dj.jpg",
            ],
            [
                'nombre' => 'Cuarteto Picasso',
                'categoria' => 'musica',
                'descripcion' => 'Cuarteto de cuerda de Malaga capital para ceremonia y coctel. Repertorio clasico, jazz y versiones flamencas y pop.',
                'precio' => 1500.00,
                'foto' => "$base/cuarteto.jpg",
            ],

            // === Fotografia y video (2) ===
            [
                'nombre' => 'Estudio Costa del Sol',
                'categoria' => 'fotografia',
                'descripcion' => 'Estudio en Rincon de la Victoria. Reportaje completo (preboda + boda) con dos fotografos. Album impreso incluido.',
                'precio' => 2300.00,
                'foto' => "$base/fotografo1.jpg",
            ],
            [
                'nombre' => 'Visual Andalucia',
                'categoria' => 'fotografia',
                'descripcion' => 'Productora audiovisual de Velez-Malaga. Video cinematografico 4K con dron sobre la Axarquia. Resumen de 5 minutos + pelicula completa.',
                'precio' => 1900.00,
                'foto' => "$base/fotografo2.jpg",
            ],

            // === Catering y menu (3) ===
            [
                'nombre' => 'Catering Mengual',
                'categoria' => 'catering',
                'descripcion' => 'Catering de referencia en Malaga. Menu degustacion de 9 pases con maridaje de vinos D.O. Sierras de Malaga. Servicio formal.',
                'precio' => 9800.00,
                'foto' => "$base/catering.jpg",
            ],
            [
                'nombre' => 'Sabores de la Axarquia',
                'categoria' => 'catering',
                'descripcion' => 'Coctel andaluz en Velez-Malaga con 25 variedades: estaciones de jamon iberico, espetos, ensaladas malagueñas y sushi mediterraneo.',
                'precio' => 7400.00,
                'foto' => "$base/catering2.jpg",
            ],
            [
                'nombre' => 'Cocina del Mar',
                'categoria' => 'catering',
                'descripcion' => 'Catering especializado en pescado y marisco fresco de la lonja de Caleta de Velez. Menu de 7 pases con producto del dia.',
                'precio' => 8500.00,
                'foto' => "$base/comida.jpg",
            ],

            // === Decoracion (3) ===
            [
                'nombre' => 'Decoeventos Malaga',
                'categoria' => 'decoracion',
                'descripcion' => 'Estudio de decoracion en Malaga capital. Diseño integral: photocall, mesa de bienvenida, seating plan y decoracion de mesas.',
                'precio' => 1700.00,
                'foto' => "$base/decoracion.jpg",
            ],
            [
                'nombre' => 'Altares de Andalucia',
                'categoria' => 'decoracion',
                'descripcion' => 'Especialistas en altares y arcos para ceremonias civiles y religiosas. Estructuras de madera natural decoradas con flor y tela.',
                'precio' => 1400.00,
                'foto' => "$base/altar3.jpg",
            ],
            [
                'nombre' => 'Encanto Mediterraneo',
                'categoria' => 'decoracion',
                'descripcion' => 'Decoracion integral con un estilo mediterraneo y bohemio. Mobiliario chill-out, lamparas vintage y caminos de mesa artesanos.',
                'precio' => 2000.00,
                'foto' => "$base/decorar.jpg",
            ],

            // === Vestuario (2) ===
            [
                'nombre' => 'Atelier Mar y Sal',
                'categoria' => 'vestuario',
                'descripcion' => 'Atelier en Rincon de la Victoria. Asesoramiento personalizado de imagen para los novios y acceso a coleccion exclusiva mediterranea.',
                'precio' => 700.00,
                'foto' => "$base/atelier.jpg",
            ],
            [
                'nombre' => 'Atelier Costa Brava',
                'categoria' => 'vestuario',
                'descripcion' => 'Confeccion a medida de vestidos de novia y trajes de novio en Malaga. Pruebas ilimitadas y arreglos finales incluidos.',
                'precio' => 1200.00,
                'foto' => "$base/atelier1.jpg",
            ],

            // === Coches y transporte (2) ===
            [
                'nombre' => 'Clasicos Costa del Sol',
                'categoria' => 'transporte',
                'descripcion' => 'Coche de epoca con chofer en Malaga (Mercedes 280 SE de 1972). 4 horas de servicio entre la iglesia y la finca.',
                'precio' => 900.00,
                'foto' => "$base/coche.jpg",
            ],
            [
                'nombre' => 'Coches Vintage Malaga',
                'categoria' => 'transporte',
                'descripcion' => 'Flota de coches clasicos para los novios e invitados. Rolls Royce, Jaguar y Cadillac con chofer uniformado.',
                'precio' => 1100.00,
                'foto' => "$base/coche1.jpg",
            ],

            // === Detalles para invitados (2) ===
            [
                'nombre' => 'Detalles Mediterraneos',
                'categoria' => 'detalles',
                'descripcion' => 'Recordatorios artesanos de la Axarquia: aceite virgen extra de Periana o miel de caña de Frigiliana en frasco con etiqueta personalizada.',
                'precio' => 700.00,
                'foto' => "$base/detalles.jpg",
            ],
            [
                'nombre' => 'Recuerdos del Sur',
                'categoria' => 'detalles',
                'descripcion' => 'Cajitas personalizadas con dulces tipicos de Malaga: peladillas de Casarabonela, almendras garrapinadas y mostachones de Utrera.',
                'precio' => 850.00,
                'foto' => "$base/detalles2.jpg",
            ],

            // === Tarta nupcial (3) ===
            [
                'nombre' => 'Pasteleria La Canela',
                'categoria' => 'tarta',
                'descripcion' => 'Pasteleria artesana de Velez-Malaga. Tarta de 3 pisos con buttercream y flores naturales. Sabores a elegir (red velvet, limon, chocolate).',
                'precio' => 420.00,
                'foto' => "$base/Tarta.jpg",
            ],
            [
                'nombre' => 'Dulce Tradicion',
                'categoria' => 'tarta',
                'descripcion' => 'Tartas de boda con tecnica fondant y diseños personalizados. 4 pisos con detalles dorados y figuras a medida.',
                'precio' => 580.00,
                'foto' => "$base/Tarta2.jpg",
            ],
            [
                'nombre' => 'La Tarteria Malaga',
                'categoria' => 'tarta',
                'descripcion' => 'Mesa dulce completa: tarta principal, cupcakes, macarons y bombones artesanos. Decoracion floral incluida.',
                'precio' => 750.00,
                'foto' => "$base/tartas.jpg",
            ],
        ];

        foreach ($proveedores as $proveedor) {
            // firstOrCreate: si ya existe un proveedor con ese nombre lo deja
            // como esta (preserva ediciones manuales). Si no existe, lo crea.
            Proveedor::firstOrCreate(
                ['nombre' => $proveedor['nombre']],
                $proveedor,
            );
        }
    }
}
