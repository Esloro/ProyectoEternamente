<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Añade soft-delete a la tabla usuarios. Al eliminar una cuenta no se
 * borra fisicamente la fila: se anonimizan los datos personales y se
 * marca deleted_at, asi mantenemos el historial de bodas, mensajes de
 * contacto, etc. ligado a un id consistente.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $tabla) {
            $tabla->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $tabla) {
            $tabla->dropSoftDeletes();
        });
    }
};
