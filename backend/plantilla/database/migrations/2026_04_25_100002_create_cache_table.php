<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tablas de infraestructura de Laravel (cache y locks). Nombres en inglés
// porque Laravel los referencia internamente. Se generan así por defecto.
return new class extends Migration {
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $tabla) {
            $tabla->string('key')->primary();
            $tabla->mediumText('value');
            $tabla->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $tabla) {
            $tabla->string('key')->primary();
            $tabla->string('owner');
            $tabla->integer('expiration');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
    }
};
