<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('eventos_organizacion') && !Schema::hasColumn('eventos_organizacion', 'destinatario_tipo')) {
            Schema::table('eventos_organizacion', function (Blueprint $table) {
                $table->string('destinatario_tipo', 50)->default('organizacion_completa')->nullable()->after('enlace_virtual');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('eventos_organizacion') && Schema::hasColumn('eventos_organizacion', 'destinatario_tipo')) {
            Schema::table('eventos_organizacion', function (Blueprint $table) {
                $table->dropColumn('destinatario_tipo');
            });
        }
    }
};
