<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('terrenos', function (Blueprint $table) {
            $table->json('poligono')->nullable()->after('longitud');
        });
    }

    public function down(): void
    {
        Schema::table('terrenos', function (Blueprint $table) {
            $table->dropColumn('poligono');
        });
    }
};
