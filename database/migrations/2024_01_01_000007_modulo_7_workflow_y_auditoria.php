<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 50);
            $table->tinyInteger('estado')->default(0);
            $table->foreignId('solicitante_usuario_id')->constrained('usuarios');
            $table->foreignId('destinatario_usuario_id')->nullable()->constrained('usuarios');
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones');
            $table->json('datos_extra')->nullable();
            $table->timestamp('fecha_solicitud')->nullable()->useCurrent();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->timestamps();
        });

        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->foreignId('solicitud_id')->nullable()->constrained('solicitudes');
            $table->string('titulo', 100);
            $table->text('mensaje');
            $table->string('tipo', 50);
            $table->tinyInteger('leido')->default(0);
            $table->timestamps();
        });

        Schema::create('historial_procesos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones');
            $table->string('tabla_afectada', 50);
            $table->integer('registro_id');
            $table->string('accion', 20);
            $table->text('descripcion');
            $table->json('detalles_previos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_procesos');
        Schema::dropIfExists('notificaciones');
        Schema::dropIfExists('solicitudes');
    }
};
