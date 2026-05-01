<?php

namespace Database\Seeders;

use App\Models\Testimonio;
use Illuminate\Database\Seeder;

class TestimonioSeeder extends Seeder
{
    public function run(): void
    {
        $testimonios = [
            [
                'nombre_cliente' => 'Marta y Javier',
                'foto' => 'https://picsum.photos/seed/testi-marta-javier/200/200',
                'valoracion' => 5,
                'comentario' => 'Hicieron realidad la boda de nuestros sueños. Cada detalle estuvo perfectamente cuidado y pudimos disfrutar del dia sin preocuparnos por nada.',
                'verificado' => true,
            ],
            [
                'nombre_cliente' => 'Lucia y David',
                'foto' => 'https://picsum.photos/seed/testi-lucia-david/200/200',
                'valoracion' => 5,
                'comentario' => 'Profesionalidad excelente y mucha cercania. Nos sentimos acompañados desde el primer dia hasta despues de la luna de miel.',
                'verificado' => true,
            ],
            [
                'nombre_cliente' => 'Elena y Pablo',
                'foto' => 'https://picsum.photos/seed/testi-elena-pablo/200/200',
                'valoracion' => 4,
                'comentario' => 'Gran trabajo en la organizacion. La coordinacion el dia de la boda fue impecable. Los proveedores recomendados fueron todo aciertos.',
                'verificado' => true,
            ],
            [
                'nombre_cliente' => 'Ana y Sergio',
                'foto' => 'https://picsum.photos/seed/testi-ana-sergio/200/200',
                'valoracion' => 5,
                'comentario' => 'Lo recomendamos sin dudarlo. Tienen un gusto exquisito y supieron entender exactamente el estilo que queriamos.',
                'verificado' => true,
            ],
            [
                'nombre_cliente' => 'Patricia y Miguel',
                'foto' => 'https://picsum.photos/seed/testi-patricia-miguel/200/200',
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
