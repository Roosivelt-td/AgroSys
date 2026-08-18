<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Corregir Notificaciones (Cascade sobre Solicitudes)
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropForeign(['solicitud_id']);
            $table->foreign('solicitud_id')
                ->references('id')->on('solicitudes')
                ->onDelete('cascade');
        });

        // 2. Corregir Miembro Roles (Cascade sobre Miembros)
        Schema::table('miembro_roles', function (Blueprint $table) {
            $table->dropForeign(['miembro_id']);
            $table->foreign('miembro_id')
                ->references('id')->on('miembros_organizacion')
                ->onDelete('cascade');
        });

        // 3. Corregir Asignaciones de Supervisor (Cascade sobre Miembros y Usuarios)
        Schema::table('asignaciones_supervisor', function (Blueprint $table) {
            $table->dropForeign(['supervisor_miembro_id']);
            $table->dropForeign(['agricultor_usuario_id']);

            $table->foreign('supervisor_miembro_id')
                ->references('id')->on('miembros_organizacion')
                ->onDelete('cascade');

            $table->foreign('agricultor_usuario_id')
                ->references('id')->on('usuarios')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // No es estrictamente necesario volver a 'no action' para desarrollo,
        // pero se mantiene la estructura por coherencia.
    }
};
