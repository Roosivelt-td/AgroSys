<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('terrenos', function (Blueprint $table) {
            // Primero eliminamos la restricción actual para poder modificarla
            $table->dropForeign(['organizacion_id']);

            // Hacemos que el campo sea opcional
            $table->foreignId('organizacion_id')
                ->nullable()
                ->change()
                ->constrained('organizaciones')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('terrenos', function (Blueprint $table) {
            $table->dropForeign(['organizacion_id']);
            $table->foreignId('organizacion_id')
                ->nullable(false)
                ->change()
                ->constrained('organizaciones');
        });
    }
};
