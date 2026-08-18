<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos_organizacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->constrained('organizaciones');
            $table->foreignId('creado_por_usuario_id')->constrained('usuarios');
            $table->string('titulo', 150);
            $table->text('descripcion')->nullable();
            $table->dateTime('fecha_evento');
            $table->integer('duracion_minutos')->default(60);
            $table->string('lugar', 255)->nullable();
            $table->string('enlace_virtual', 500)->nullable();
            $table->timestamps();
        });

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
            $table->dateTime('fecha_respuesta')->nullable();
            $table->text('comentario_agricultor')->nullable();
            $table->timestamps();
        });

        Schema::create('conversaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones');
            $table->string('tipo_conversacion', 20);
            $table->string('nombre_grupo', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('conversaciones_participantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversacion_id')->constrained('conversaciones');
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->timestamp('ultima_lectura')->nullable();
            $table->tinyInteger('abandonado')->default(0);
            $table->timestamps();
        });

        Schema::create('mensajes_chat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversacion_id')->constrained('conversaciones');
            $table->foreignId('remitente_usuario_id')->nullable()->constrained('usuarios');
            $table->tinyInteger('es_ia')->default(0);
            $table->text('mensaje');
            $table->foreignId('archivo_adjunto_id')->nullable()->constrained('archivos_multimedia');
            $table->tinyInteger('leido')->default(0);
            $table->dateTime('editado_at')->nullable();
            $table->tinyInteger('eliminado_todos')->default(0);
            $table->timestamps();
        });

        Schema::create('mensajes_borrados_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mensaje_id')->constrained('mensajes_chat')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes_chat');
        Schema::dropIfExists('conversaciones_participantes');
        Schema::dropIfExists('conversaciones');
        Schema::dropIfExists('sugerencias_tareas');
        Schema::dropIfExists('eventos_organizacion');
    }
};
