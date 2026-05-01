<?php

namespace Database\Seeders;

use App\Models\Testimonio;
use Illuminate\Database\Seeder;

class TestimonioSeeder extends Seeder
{
    public function run(): void
    {
        Testimonio::truncate();

        $testimonios = [
            [
                'nombre_cliente' => 'Marta y Javier',
                'foto' => 'https://images.unsplash.com/photo-1529634806980-85c3dd6d34ac?w=200&h=200&fit=crop&crop=faces',
                'valoracion' => 5,
                'comentario' => 'Hicieron realidad la boda de nuestros sueños. Cada detalle estuvo perfectamente cuidado y pudimos disfrutar del dia sin preocuparnos por nada.',
                'verificado' => true,
            ],
            [
                'nombre_cliente' => 'Lucia y David',
                'foto' => 'https://images.unsplash.com/photo-1525258498534-ba06de4a3e42?w=200&h=200&fit=crop&crop=faces',
                'valoracion' => 5,
                'comentario' => 'Profesionalidad excelente y mucha cercania. Nos sentimos acompañados desde el primer dia hasta despues de la luna de miel.',
                'verificado' => true,
            ],
            [
                'nombre_cliente' => 'Elena y Pablo',
                'foto' => 'https://images.unsplash.com/photo-1516589091380-5d8e87df6999?w=200&h=200&fit=crop&crop=faces',
                'valoracion' => 4,
                'comentario' => 'Gran trabajo en la organizacion. La coordinacion el dia de la boda fue impecable. Los proveedores recomendados fueron todo aciertos.',
                'verificado' => true,
            ],
            [
                'nombre_cliente' => 'Ana y Sergio',
                'foto' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&h=200&fit=crop&crop=faces',
                'valoracion' => 5,
                'comentario' => 'Lo recomendamos sin dudarlo. Tienen un gusto exquisito y supieron entender exactamente el estilo que queriamos.',
                'verificado' => true,
            ],
            [
                'nombre_cliente' => 'Patricia y Miguel',
                'foto' => 'https://images.unsplash.com/photo-1582015752624-e8b1b03a9b93?w=200&h=200&fit=crop&crop=faces',
                'valoracion' => 5,
                'comentario' => 'Despues de planificar nuestra boda con ellos no podemos imaginar haberlo hecho de otra manera. ¡Mil gracias!',
                'verificado' => true,
            ],
        ];

        foreach ($testimonios as $testimonio) {
            Testimonio::create($testimonio);
        }
    }
}
