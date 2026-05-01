<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portfolio', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->string('titulo', 150);
            $tabla->text('descripcion')->nullable();
            $tabla->string('foto');
            $tabla->unsignedInteger('orden')->default(0);
            $tabla->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio');
    }
};
