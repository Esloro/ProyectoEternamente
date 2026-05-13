<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        Portfolio::truncate();

        // Las imagenes viven en frontend/public/bodas/ y las sirve Vercel.
        // Guardamos rutas relativas: el <img src> las resuelve contra el dominio
        // del frontend (no contra APP_URL del backend), asi funcionan en local
        // y en produccion sin depender de la configuracion del droplet.
        $entradas = [
            [
                'titulo'      => 'Boda de Lucia y David',
                'descripcion' => 'Ceremonia clasica en iglesia barroca con paniculata blanca decorando los bancos.',
                'foto'        => '/bodas/portfolio1.jpg',
                'orden'       => 1,
            ],
            [
                'titulo'      => 'Boda de Marta y Javier',
                'descripcion' => 'Ceremonia al aire libre bajo un arco floral en los jardines de la finca.',
                'foto'        => '/bodas/portfolio2.jpg',
                'orden'       => 2,
            ],
            [
                'titulo'      => 'Boda de Elena y Pablo',
                'descripcion' => 'Beso bajo el velo frente a un palacete historico tras la ceremonia civil.',
                'foto'        => '/bodas/portfolio3.jpg',
                'orden'       => 3,
            ],
            [
                'titulo'      => 'Boda de Ana y Sergio',
                'descripcion' => 'Despedida nocturna entre bengalas tras una elegante recepcion de gala.',
                'foto'        => '/bodas/portfolio4.jpg',
                'orden'       => 4,
            ],
            [
                'titulo'      => 'Boda de Carmen y Andres',
                'descripcion' => 'Intercambio de anillos en una ceremonia intima con altar floral en blanco.',
                'foto'        => '/bodas/portfolio5.jpg',
                'orden'       => 5,
            ],
            [
                'titulo'      => 'Boda de Beatriz y Fernando',
                'descripcion' => 'Sesion preboda junto a un coche clasico en una avenida arbolada.',
                'foto'        => '/bodas/portfolio6.jpg',
                'orden'       => 6,
            ],
        ];

        foreach ($entradas as $entrada) {
            Portfolio::create($entrada);
        }
    }
}
