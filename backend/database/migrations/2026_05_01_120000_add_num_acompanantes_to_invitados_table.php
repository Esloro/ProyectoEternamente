<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Sustituye el booleano `acompanante` por un entero `num_acompanantes`
     * para que cada invitado pueda traer N acompañantes (no solo 1).
     */
    public function up(): void
    {
        Schema::table('invitados', function (Blueprint $tabla) {
            $tabla->unsignedSmallInteger('num_acompanantes')->default(0)->after('alergias');
        });

        // Convertimos los datos existentes: acompanante=1 -> num_acompanantes=1.
        DB::statement('UPDATE invitados SET num_acompanantes = 1 WHERE acompanante = 1');

        Schema::table('invitados', function (Blueprint $tabla) {
            $tabla->dropColumn('acompanante');
        });
    }

    public function down(): void
    {
        Schema::table('invitados', function (Blueprint $tabla) {
            $tabla->boolean('acompanante')->default(false)->after('alergias');
        });

        DB::statement('UPDATE invitados SET acompanante = 1 WHERE num_acompanantes > 0');

        Schema::table('invitados', function (Blueprint $tabla) {
            $tabla->dropColumn('num_acompanantes');
        });
    }
};
