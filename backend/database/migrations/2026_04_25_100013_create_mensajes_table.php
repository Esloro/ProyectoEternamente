<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Chat interno (cliente <-> wedding planner). Sin WebSockets: el frontend
// hará polling cada 5-10s. La columna `leido` permite mostrar contador de
// mensajes pendientes.
return new class extends Migration {
    public function up(): void
    {
        Schema::create('mensajes', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->foreignId('boda_id')->constrained('bodas')->cascadeOnDelete();
            $tabla->foreignId('emisor_id')->constrained('usuarios')->cascadeOnDelete();
            $tabla->foreignId('receptor_id')->constrained('usuarios')->cascadeOnDelete();
            $tabla->text('contenido');
            $tabla->boolean('leido')->default(false);
            $tabla->timestamps();

            // Para ordenar el hilo y filtrar pendientes rápidamente.
            $tabla->index(['boda_id', 'created_at']);
            $tabla->index(['receptor_id', 'leido']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};
