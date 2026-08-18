<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compradores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200);
            $table->string('ruc_dni', 20)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('direccion')->nullable();
            $table->timestamps();
        });

        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cosecha_id')->constrained('cosechas');
            $table->foreignId('comprador_id')->constrained('compradores');
            $table->integer('fecha_venta');
            $table->decimal('cantidad_vendida_kg', 12, 2);
            $table->decimal('precio_por_kg', 12, 2);
            $table->decimal('costo_flete', 12, 2);
            $table->decimal('impuestos', 12, 2);
            $table->enum('comprobante_tipo', ['boleta', 'factura', 'ticket'])->default('boleta');
            $table->string('comprobante_numero', 50)->nullable();
            $table->string('foto_path', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('clima_registros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('terreno_id')->constrained('terrenos')->onDelete('cascade');
            $table->dateTime('fecha_hora')->useCurrent();
            $table->decimal('temperatura', 5, 2)->nullable();
            $table->integer('humedad')->nullable();
            $table->decimal('viento_kmh', 5, 2)->nullable();
            $table->decimal('presion_hpa', 6, 2)->nullable();
            $table->integer('prob_lluvia')->nullable();
            $table->decimal('precipitacion_mm', 6, 2)->nullable();
            $table->string('condicion', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clima_registros');
        Schema::dropIfExists('ventas');
        Schema::dropIfExists('compradores');
    }
};
