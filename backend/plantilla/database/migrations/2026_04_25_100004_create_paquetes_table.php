<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('paquetes', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->string('nombre', 100);
            $tabla->text('descripcion');
            $tabla->decimal('precio', 10, 2);
            $tabla->json('caracteristicas')->nullable();
            $tabla->boolean('destacado')->default(false);
            $tabla->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paquetes');
    }
};
