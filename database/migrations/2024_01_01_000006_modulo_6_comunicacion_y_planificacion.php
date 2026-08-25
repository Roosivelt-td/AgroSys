<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * MODULO 6: Comunicación y Planificación
     * Gestiona la interaccion entre usuarios y la agenda del equipo.
     */
    public function up(): void
    {
        // 10. conversaciones: Canales de comunicacion (Privados, Grupales, IA)
        Schema::create('conversaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones');
            $table->string('tipo_conversacion', 20); // individual, privada, grupal, ia
            $table->string('nombre_grupo', 100)->nullable();
            $table->timestamps();
        });

        // 11. conversaciones_participantes: Usuarios dentro de cada hilo de chat
        Schema::create('conversaciones_participantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversacion_id')->constrained('conversaciones');
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->timestamp('ultima_lectura')->nullable();
            $table->timestamp('limpiado_at')->nullable();
            $table->tinyInteger('abandonado')->default(0);
            $table->timestamps();
        });

        // 22. mensajes_chat: El contenido de la comunicacion
        Schema::create('mensajes_chat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversacion_id')->constrained('conversaciones');
            $table->foreignId('remitente_usuario_id')->nullable()->constrained('usuarios');
            $table->tinyInteger('es_ia')->default(0);
            $table->text('mensaje');
            $table->foreignId('archivo_adjunto_id')->nullable()->constrained('archivos_multimedia');
            $table->tinyInteger('leido')->default(0);
            $table->datetime('editado_at')->nullable();
            $table->tinyInteger('eliminado_todos')->default(0);
            $table->timestamps();
        });

        // 21. mensajes_borrados_usuario: Trazabilidad de mensajes ocultados por el usuario
        Schema::create('mensajes_borrados_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mensaje_id')->constrained('mensajes_chat')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->timestamps();
        });

        // 25. notificaciones: Alertas en la campanita para acciones relevantes
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->unsignedBigInteger('solicitud_id')->nullable(); // Relacion con solicitudes
            $table->string('titulo', 100);
            $table->text('mensaje');
            $table->string('tipo', 50);
            $table->tinyInteger('leido')->default(0);
            $table->timestamps();
        });

        // 14. eventos_organizacion: Agenda compartida, reuniones y capacitaciones
        Schema::create('eventos_organizacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->constrained('organizaciones');
            $table->foreignId('creado_por_usuario_id')->constrained('usuarios');
            $table->string('titulo', 150);
            $table->text('descripcion')->nullable();
            $table->datetime('fecha_evento');
            $table->integer('duracion_minutos')->default(60);
            $table->string('lugar', 255)->nullable();
            $table->string('enlace_virtual', 500)->nullable();
            $table->timestamps();
        });

        // 32. sugerencias_tareas: Canal de ordenes de trabajo entre Supervisor y Agricultor
        Schema::create('sugerencias_tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->constrained('organizaciones');
            $table->foreignId('supervisor_usuario_id')->constrained('usuarios');
            $table->foreignId('agricultor_usuario_id')->constrained('usuarios');
            $table->foreignId('cultivo_id')->nullable()->constrained('cultivos');
            $table->string('titulo', 100);
            $table->text('descripcion');
            $table->date('fecha_sugerida');
            $table->tinyInteger('estado')->default(0);
            $table->datetime('fecha_respuesta')->nullable();
            $table->text('comentario_agricultor')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sugerencias_tareas');
        Schema::dropIfExists('eventos_organizacion');
        Schema::dropIfExists('notificaciones');
        Schema::dropIfExists('mensajes_borrados_usuario');
        Schema::dropIfExists('mensajes_chat');
        Schema::dropIfExists('conversaciones_participantes');
        Schema::dropIfExists('conversaciones');
    }
};
