<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Cola de trabajos. Necesario para enviar emails (verificación, contacto)
// de forma asíncrona en producción. En dev podemos usar QUEUE_CONNECTION=sync.
return new class extends Migration {
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->string('queue')->index();
            $tabla->longText('payload');
            $tabla->unsignedTinyInteger('attempts');
            $tabla->unsignedInteger('reserved_at')->nullable();
            $tabla->unsignedInteger('available_at');
            $tabla->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $tabla) {
            $tabla->string('id')->primary();
            $tabla->string('name');
            $tabla->integer('total_jobs');
            $tabla->integer('pending_jobs');
            $tabla->integer('failed_jobs');
            $tabla->longText('failed_job_ids');
            $tabla->mediumText('options')->nullable();
            $tabla->integer('cancelled_at')->nullable();
            $tabla->integer('created_at');
            $tabla->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->string('uuid')->unique();
            $tabla->text('connection');
            $tabla->text('queue');
            $tabla->longText('payload');
            $tabla->longText('exception');
            $tabla->timestamp('failed_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
    }
};
