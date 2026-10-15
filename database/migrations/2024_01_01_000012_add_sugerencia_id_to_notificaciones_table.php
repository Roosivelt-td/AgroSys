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
        if (Schema::hasTable('notificaciones') && !Schema::hasColumn('notificaciones', 'sugerencia_id')) {
            Schema::table('notificaciones', function (Blueprint $table) {
                $table->unsignedBigInteger('sugerencia_id')->nullable()->after('solicitud_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('notificaciones') && Schema::hasColumn('notificaciones', 'sugerencia_id')) {
            Schema::table('notificaciones', function (Blueprint $table) {
                $table->dropColumn('sugerencia_id');
            });
        }
    }
};
