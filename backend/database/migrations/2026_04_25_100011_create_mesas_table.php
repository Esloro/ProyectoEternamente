<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mesas', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->foreignId('boda_id')->constrained('bodas')->cascadeOnDelete();
            $tabla->unsignedInteger('numero');
            $tabla->unsignedInteger('capacidad');
            $tabla->timestamps();

            // Una boda no puede tener dos mesas con el mismo número.
            $tabla->unique(['boda_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesas');
    }
};
