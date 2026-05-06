<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        Portfolio::truncate();

        $entradas = [
            [
                'titulo'      => 'Boda de Lucia y David',
                'descripcion' => 'Ceremonia clasica en iglesia barroca con paniculata blanca decorando los bancos.',
                'foto'        => asset('storage/bodas/portfolio1.jpg'),
                'orden'       => 1,
            ],
            [
                'titulo'      => 'Boda de Marta y Javier',
                'descripcion' => 'Ceremonia al aire libre bajo un arco floral en los jardines de la finca.',
                'foto'        => asset('storage/bodas/portfolio2.jpg'),
                'orden'       => 2,
            ],
            [
                'titulo'      => 'Boda de Elena y Pablo',
                'descripcion' => 'Beso bajo el velo frente a un palacete historico tras la ceremonia civil.',
                'foto'        => asset('storage/bodas/portfolio3.jpg'),
                'orden'       => 3,
            ],
            [
                'titulo'      => 'Boda de Ana y Sergio',
                'descripcion' => 'Despedida nocturna entre bengalas tras una elegante recepcion de gala.',
                'foto'        => asset('storage/bodas/portfolio4.jpg'),
                'orden'       => 4,
            ],
            [
                'titulo'      => 'Boda de Carmen y Andres',
                'descripcion' => 'Intercambio de anillos en una ceremonia intima con altar floral en blanco.',
                'foto'        => asset('storage/bodas/portfolio5.jpg'),
                'orden'       => 5,
            ],
            [
                'titulo'      => 'Boda de Beatriz y Fernando',
                'descripcion' => 'Sesion preboda junto a un coche clasico en una avenida arbolada.',
                'foto'        => asset('storage/bodas/portfolio6.jpg'),
                'orden'       => 6,
            ],
        ];

        foreach ($entradas as $entrada) {
            Portfolio::create($entrada);
        }
    }
}
