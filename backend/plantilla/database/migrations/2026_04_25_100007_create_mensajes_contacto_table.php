<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mensajes_contacto', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->string('nombre', 100);
            $tabla->string('email', 150);
            $tabla->string('telefono', 20)->nullable();
            $tabla->text('mensaje');
            $tabla->boolean('leido')->default(false);
            $tabla->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes_contacto');
    }
};
