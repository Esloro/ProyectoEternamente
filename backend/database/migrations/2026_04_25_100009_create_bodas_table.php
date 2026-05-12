<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bodas', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();

            // Datos del cuestionario inicial
            $tabla->string('nombre_pareja', 150)->nullable();
            $tabla->enum('tipo_ceremonia', [
                'religiosa',
                'civil_ayuntamiento',
                'simbolica',
                'renovacion_votos',
            ]);
            $tabla->enum('lugar_celebracion', [
                'iglesia',
                'ayuntamiento',
                'finca',
                'playa',
                'jardin',
                'restaurante',
                'otro',
            ]);
            $tabla->date('fecha_boda');
            $tabla->unsignedInteger('num_invitados');

            // Sin tildes para evitar problemas de codificación con ENUM en MySQL.
            // El frontend puede mostrar "Mañana" en lugar de "manana".
            $tabla->enum('franja_horaria', ['manana', 'tarde', 'noche']);

            $tabla->enum('tematica', ['clasica', 'rustica', 'moderna', 'boho', 'glamour']);
            $tabla->enum('tipo_comida', ['coctel', 'banquete', 'buffet', 'familiar']);
            $tabla->enum('presupuesto_orientativo', [
                'hasta_10000',
                '10000_20000',
                '20000_35000',
                '35000_50000',
                'mas_50000',
            ]);

            // Estado de la boda — clave del flujo de negocio.
            // pendiente_reunion -> el cliente solo ve "estamos en contacto"
            // activa            -> el admin la ha confirmado tras la reunión
            // finalizada        -> boda celebrada
            // cancelada         -> no se llegó a contratar
            $tabla->enum('estado', [
                'pendiente_reunion',
                'activa',
                'finalizada',
                'cancelada',
            ])->default('pendiente_reunion');

            $tabla->decimal('presupuesto_estimado', 10, 2)->default(0);
            $tabla->decimal('presupuesto_definitivo', 10, 2)->nullable();

            $tabla->timestamps();

            $tabla->index('estado');
            $tabla->index('fecha_boda');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bodas');
    }
};
