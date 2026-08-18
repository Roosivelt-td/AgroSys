<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Añadir columnas de edición y borrado global si no existen
        Schema::table('mensajes_chat', function (Blueprint $table) {
            if (!Schema::hasColumn('mensajes_chat', 'editado_at')) {
                $table->dateTime('editado_at')->nullable()->after('leido');
            }
            if (!Schema::hasColumn('mensajes_chat', 'eliminado_todos')) {
                $table->tinyInteger('eliminado_todos')->default(0)->after('editado_at');
            }
        });

        // 2. Crear tabla para "Eliminar para mí"
        if (!Schema::hasTable('mensajes_borrados_usuario')) {
            Schema::create('mensajes_borrados_usuario', function (Blueprint $table) {
                $table->id();
                $table->foreignId('mensaje_id')->constrained('mensajes_chat')->onDelete('cascade');
                $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes_borrados_usuario');
        Schema::table('mensajes_chat', function (Blueprint $table) {
            $table->dropColumn(['editado_at', 'eliminado_todos']);
        });
    }
};
