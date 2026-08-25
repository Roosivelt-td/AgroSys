<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * MODULO 7: Workflow y Auditoría
     * Registro forense e inmutable de todos los movimientos del sistema.
     */
    public function up(): void
    {
        // 31. solicitudes: Motor de tramites (Crear empresa, Ascensos, Invitaciones)
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 50); // creacion_organizacion, unirse_organizacion, etc
            $table->tinyInteger('estado')->default(0); // 0: pendiente, 1: aprobada, 2: rechazada
            $table->foreignId('solicitante_usuario_id')->constrained('usuarios');
            $table->foreignId('destinatario_usuario_id')->nullable()->constrained('usuarios');
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones');
            $table->json('datos_extra')->nullable();
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->timestamps();
        });

        // 15. historial_procesos: El "Diario Eterno" de acciones para transparencia total
        Schema::create('historial_procesos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones');
            $table->string('tabla_afectada', 50);
            $table->integer('registro_id');
            $table->string('accion', 20); // INSERT, UPDATE, DELETE, LOGIN, etc
            $table->text('descripcion');
            $table->json('detalles_previos')->nullable();
            $table->timestamps();
        });

        // Relacionar notificaciones con solicitudes (Foreign Key diferida)
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->foreign('solicitud_id')->references('id')->on('solicitudes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropForeign(['solicitud_id']);
        });
        Schema::dropIfExists('historial_procesos');
        Schema::dropIfExists('solicitudes');
    }
};
