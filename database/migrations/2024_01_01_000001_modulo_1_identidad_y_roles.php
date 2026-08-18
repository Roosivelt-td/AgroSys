<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rol', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('organizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->string('ruc', 20)->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->text('direccion')->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')->constrained('rol');
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('email', 255)->unique();
            $table->string('password');
            $table->string('telefono', 20)->nullable();
            $table->string('dni', 20)->unique()->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->integer('experiencia_anios')->default(0);
            $table->string('nivel_educativo', 50)->nullable();
            $table->text('ubicacion')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('foto_perfil_url', 255)->nullable();
            $table->string('foto_portada_url', 255)->nullable();
            $table->boolean('is_activo')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('roles_organizacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('miembros_organizacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->foreignId('organizacion_id')->constrained('organizaciones');
            $table->tinyInteger('es_propietario')->default(0);
            $table->tinyInteger('estado')->default(1);
            $table->date('fecha_ingreso')->nullable();
            $table->unique(['usuario_id', 'organizacion_id'], 'unique_usuario_org');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('miembro_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('miembro_id')->constrained('miembros_organizacion');
            $table->foreignId('rol_id')->constrained('roles_organizacion');
            $table->tinyInteger('estado')->default(1);
            $table->unique(['miembro_id', 'rol_id'], 'unique_miembro_rol');
            $table->timestamps();
        });

        Schema::create('asignaciones_supervisor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->constrained('organizaciones');
            $table->foreignId('supervisor_miembro_id')->constrained('miembros_organizacion');
            $table->foreignId('agricultor_usuario_id')->constrained('usuarios');
            $table->timestamps();
        });

        // Laravel default tables
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('asignaciones_supervisor');
        Schema::dropIfExists('miembro_roles');
        Schema::dropIfExists('miembros_organizacion');
        Schema::dropIfExists('roles_organizacion');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('organizaciones');
        Schema::dropIfExists('rol');
    }
};
