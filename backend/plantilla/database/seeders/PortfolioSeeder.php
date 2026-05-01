<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        // 8 entradas de portfolio (fotos placeholder de picsum.photos).
        $entradas = [
            ['titulo' => 'Boda de Lucia y David',         'descripcion' => 'Ceremonia clasica en iglesia y banquete en finca rural de Madrid.',           'foto' => 'https://picsum.photos/seed/boda-lucia-david/1200/800',        'orden' => 1],
            ['titulo' => 'Boda de Marta y Javier',        'descripcion' => 'Boda boho al aire libre en una hacienda andaluza.',                          'foto' => 'https://picsum.photos/seed/boda-marta-javier/1200/800',       'orden' => 2],
            ['titulo' => 'Boda de Elena y Pablo',         'descripcion' => 'Boda de inspiracion industrial en una antigua fabrica reformada.',           'foto' => 'https://picsum.photos/seed/boda-elena-pablo/1200/800',        'orden' => 3],
            ['titulo' => 'Boda de Ana y Sergio',          'descripcion' => 'Ceremonia frente al mar con coctel sobre la arena.',                         'foto' => 'https://picsum.photos/seed/boda-ana-sergio/1200/800',         'orden' => 4],
            ['titulo' => 'Boda de Patricia y Miguel',     'descripcion' => 'Boda intima de invierno en un castillo medieval iluminado con velas.',       'foto' => 'https://picsum.photos/seed/boda-patricia-miguel/1200/800',    'orden' => 5],
            ['titulo' => 'Boda de Sofia y Roberto',       'descripcion' => 'Boda glamour en hotel de cinco estrellas con cena de gala.',                'foto' => 'https://picsum.photos/seed/boda-sofia-roberto/1200/800',      'orden' => 6],
            ['titulo' => 'Boda de Carmen y Andres',       'descripcion' => 'Boda rustica en un cortijo con decoracion en madera y flores silvestres.',   'foto' => 'https://picsum.photos/seed/boda-carmen-andres/1200/800',      'orden' => 7],
            ['titulo' => 'Boda de Beatriz y Fernando',    'descripcion' => 'Boda moderna minimalista en una galeria de arte contemporaneo.',             'foto' => 'https://picsum.photos/seed/boda-beatriz-fernando/1200/800',   'orden' => 8],
        ];

        foreach ($entradas as $entrada) {
            Portfolio::create($entrada);
        }
    }
}
