
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clima_pronosticos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('terreno_id')->constrained('terrenos')->onDelete('cascade');
            $table->date('fecha');
            $table->decimal('temp_max', 5, 2);
            $table->decimal('temp_min', 5, 2);
            $table->integer('prob_lluvia');
            $table->string('condicion', 100);
            $table->string('icon', 50);
            $table->timestamps();

            $table->unique(['terreno_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clima_pronosticos');
    }
};
