<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invitados', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->foreignId('boda_id')->constrained('bodas')->cascadeOnDelete();
            $tabla->string('nombre', 150);
            $tabla->text('alergias')->nullable();
            $tabla->boolean('acompanante')->default(false);

            // Si se borra la mesa, el invitado queda sin asignar (no se borra).
            $tabla->foreignId('mesa_id')->nullable()->constrained('mesas')->nullOnDelete();

            $tabla->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitados');
    }
};
