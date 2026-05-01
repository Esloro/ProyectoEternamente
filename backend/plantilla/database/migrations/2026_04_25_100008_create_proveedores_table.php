<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->string('nombre', 150);
            // 10 categorías cubiertas en el panel del cliente.
            $tabla->enum('categoria', [
                'lugar',
                'floristeria',
                'musica',
                'fotografia',
                'catering',
                'decoracion',
                'vestuario',
                'transporte',
                'detalles',
                'tarta',
            ]);
            $tabla->text('descripcion')->nullable();
            $tabla->string('foto')->nullable();
            $tabla->decimal('precio', 10, 2);
            $tabla->boolean('activo')->default(true);
            $tabla->timestamps();

            $tabla->index('categoria');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
