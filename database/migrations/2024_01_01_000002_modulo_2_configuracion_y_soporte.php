<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * MODULO 2: Configuración Global y Soporte
     * Maneja la identidad visual y alertas tecnicas de la plataforma.
     */
    public function up(): void
    {
        // 09. configuracion_sistema: Parámetros de identidad visual y versión de la plataforma
        Schema::create('configuracion_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_sistema', 100);
            $table->string('lema_sistema', 255)->nullable();
            $table->string('version_sistema', 20)->default('1.0.0');
            $table->text('descripcion_detallada')->nullable();
            $table->string('logo_path', 255)->nullable();
            $table->string('favicon_path', 255)->nullable();
            $table->string('color_primario', 7)->default('#3498db');
            $table->string('color_secundario', 7)->default('#2ecc71');
            $table->foreignId('updated_by_usuario_id')->constrained('usuarios');
            $table->timestamps();
        });

        // 28. redes_sociales_sistema: Enlaces a redes sociales institucionales
        Schema::create('redes_sociales_sistema', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->string('tipo_red_social', 50);
            $table->string('url_enlace', 500);
            $table->string('icono_path', 255)->nullable();
            $table->tinyInteger('visible')->default(1);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        // 01. alertas_sistema: Mensajes globales para todos los usuarios del sistema
        Schema::create('alertas_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 100);
            $table->text('mensaje');
            $table->string('tipo_alerta', 20);
            $table->timestamp('fecha_inicio')->nullable()->useCurrent();
            $table->timestamp('fecha_fin')->nullable();
            $table->tinyInteger('visible')->default(1);
            $table->foreignId('created_by_usuario_id')->constrained('usuarios');
            $table->timestamps();
        });

        // 02. archivos_multimedia: Repositorio central de archivos con seguridad por empresa
        Schema::create('archivos_multimedia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones');
            $table->string('nombre_original', 255);
            $table->string('nombre_archivo_unique', 255);
            $table->string('ruta_completa', 500);
            $table->string('tipo_mime', 50);
            $table->bigInteger('tamano_bytes')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archivos_multimedia');
        Schema::dropIfExists('alertas_sistema');
        Schema::dropIfExists('redes_sociales_sistema');
        Schema::dropIfExists('configuracion_sistema');
    }
};
