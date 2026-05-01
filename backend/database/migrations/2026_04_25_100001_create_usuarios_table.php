<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->string('nombre', 100);
            $tabla->string('apellidos', 150);
            $tabla->string('email', 150)->unique();
            $tabla->string('telefono', 20)->nullable();
            $tabla->string('password');
            $tabla->enum('rol', ['cliente', 'administrador'])->default('cliente');
            $tabla->timestamp('email_verificado_en')->nullable();
            $tabla->string('remember_token', 100)->nullable();
            $tabla->timestamps();
        });

        // Tabla auxiliar de Laravel para reset de contraseña — la mantenemos
        // con el nombre estándar para compatibilidad con el helper Password::.
        Schema::create('password_reset_tokens', function (Blueprint $tabla) {
            $tabla->string('email')->primary();
            $tabla->string('token');
            $tabla->timestamp('created_at')->nullable();
        });

        // Sesiones (driver `database` en .env). Se mantiene en inglés porque
        // Laravel construye consultas internas contra el nombre `sessions`.
        Schema::create('sessions', function (Blueprint $tabla) {
            $tabla->string('id')->primary();
            $tabla->foreignId('user_id')->nullable()->index();
            $tabla->string('ip_address', 45)->nullable();
            $tabla->text('user_agent')->nullable();
            $tabla->longText('payload');
            $tabla->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('usuarios');
    }
};
