<?php

namespace Database\Seeders;

use App\Models\Testimonio;
use Illuminate\Database\Seeder;

class TestimonioSeeder extends Seeder
{
    public function run(): void
    {
        // IDEMPOTENTE: no borra testimonios existentes. Usa firstOrCreate por
        // nombre_cliente para no pisar los que el admin haya creado a mano.
        //
        // Las fotos viven en backend/storage/app/public/bodas/. Para que sean
        // accesibles desde el frontend hay que ejecutar `php artisan storage:link`
        // (genera el symlink public/storage -> storage/app/public).
        $base = 'http://localhost:8000/storage/bodas';

        $testimonios = [
            [
                'nombre_cliente' => 'Marta y Javier',
                'foto' => "$base/testimonio1.jpg",
                'valoracion' => 5,
                'comentario' => 'Hicieron realidad la boda de nuestros sueños. Cada detalle estuvo perfectamente cuidado y pudimos disfrutar del dia sin preocuparnos por nada.',
                'verificado' => true,
            ],
            [
                'nombre_cliente' => 'Lucia y David',
                'foto' => "$base/testimonio2.jpg",
                'valoracion' => 5,
                'comentario' => 'Profesionalidad excelente y mucha cercania. Nos sentimos acompañados desde el primer dia hasta despues de la luna de miel.',
                'verificado' => true,
            ],
            [
                'nombre_cliente' => 'Elena y Pablo',
                'foto' => "$base/tertimonio3.jpg",
                'valoracion' => 4,
                'comentario' => 'Gran trabajo en la organizacion. La coordinacion el dia de la boda fue impecable. Los proveedores recomendados fueron todo aciertos.',
                'verificado' => true,
            ],
            [
                'nombre_cliente' => 'Ana y Sergio',
                'foto' => "$base/testimonio4.jpg",
                'valoracion' => 5,
                'comentario' => 'Lo recomendamos sin dudarlo. Tienen un gusto exquisito y supieron entender exactamente el estilo que queriamos.',
                'verificado' => true,
            ],
            [
                'nombre_cliente' => 'Patricia y Miguel',
                'foto' => "$base/novios1.jpg",
                'valoracion' => 5,
                'comentario' => 'Despues de planificar nuestra boda con ellos no podemos imaginar haberlo hecho de otra manera. ¡Mil gracias!',
                'verificado' => true,
            ],
            [
                'nombre_cliente' => 'Cristina y Alejandro',
                'foto' => "$base/novios2.jpg",
                'valoracion' => 5,
                'comentario' => 'Desde la primera reunion supimos que estabamos en buenas manos. Nos guiaron sin presionar y respetaron nuestro presupuesto al detalle.',
                'verificado' => true,
            ],
            [
                'nombre_cliente' => 'Sofia y Ruben',
                'foto' => "$base/novios3.jpg",
                'valoracion' => 5,
                'comentario' => 'La decoracion supero nuestras expectativas y los proveedores que nos recomendaron fueron un acierto. Sin ellos nuestra boda no hubiera sido igual.',
                'verificado' => true,
            ],
            [
                'nombre_cliente' => 'Beatriz y Carlos',
                'foto' => "$base/novios4.jpg",
                'valoracion' => 5,
                'comentario' => 'Atencion impecable y un trato muy cercano. Resolvieron cada imprevisto con tranquilidad. Volveriamos a confiar en ellos sin dudarlo.',
                'verificado' => true,
            ],
        ];

        foreach ($testimonios as $testimonio) {
            Testimonio::firstOrCreate(
                ['nombre_cliente' => $testimonio['nombre_cliente']],
                $testimonio,
            );
        }
    }
}
