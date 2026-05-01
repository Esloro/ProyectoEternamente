<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabla pivote: cada boda elige varios proveedores y cada proveedor puede
// estar elegido en muchas bodas. Las `notas` permiten al cliente añadir
// observaciones específicas para esa contratación.
return new class extends Migration {
    public function up(): void
    {
        Schema::create('boda_proveedor', function (Blueprint $tabla) {
            $tabla->foreignId('boda_id')->constrained('bodas')->cascadeOnDelete();
            $tabla->foreignId('proveedor_id')->constrained('proveedores')->cascadeOnDelete();
            $tabla->text('notas')->nullable();
            $tabla->timestamps();

            $tabla->primary(['boda_id', 'proveedor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boda_proveedor');
    }
};
