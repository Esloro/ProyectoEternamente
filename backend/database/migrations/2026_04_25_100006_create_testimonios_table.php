<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('testimonios', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->string('nombre_cliente', 100);
            $tabla->string('foto')->nullable();
            $tabla->unsignedTinyInteger('valoracion'); // 1 a 5 estrellas
            $tabla->text('comentario');
            $tabla->boolean('verificado')->default(false);
            $tabla->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonios');
    }
};
