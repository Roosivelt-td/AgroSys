<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogo_insumos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150)->unique();
            $table->string('categoria', 50)->nullable();
            $table->string('unidad_medida', 20)->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('sincronizado')->default(false);
            $table->timestamps();
        });

        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_empresa', 200);
            $table->string('ruc', 20)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('tipo_servicio', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('insumos_usados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('labor_id')->constrained('labores')->onDelete('cascade');
            $table->foreignId('catalogo_insumo_id')->constrained('catalogo_insumos');
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->decimal('cantidad', 12, 2);
            $table->decimal('costo_unitario', 12, 2);
            $table->decimal('costo_flete', 12, 2)->default(0.00);
            $table->string('nombre_proveedor_manual', 200)->nullable();
            $table->timestamps();
        });

        Schema::create('mano_obra_tipo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->timestamps();
        });

        Schema::create('mano_obra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('labor_id')->constrained('labores')->onDelete('cascade');
            $table->foreignId('tipo_id')->constrained('mano_obra_tipo');
            $table->integer('cantidad_trabajadores');
            $table->integer('dias_trabajados');
            $table->decimal('costo_por_dia', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        Schema::create('cosechas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('labor_id')->nullable()->constrained('labores')->nullOnDelete();
            $table->integer('fecha_cosecha');
            $table->decimal('cantidad_kg', 12, 2);
            $table->string('unidad_medida', 20)->default('kg');
            $table->enum('calidad', ['primera', 'segunda', 'descarte'])->default('primera');
            $table->string('lote_codigo', 50)->nullable();
            $table->decimal('costo_operativo_cosecha', 12, 2)->default(0.00);
            $table->text('observaciones')->nullable();
            $table->string('foto_path', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cosechas');
        Schema::dropIfExists('mano_obra');
        Schema::dropIfExists('mano_obra_tipo');
        Schema::dropIfExists('insumos_usados');
        Schema::dropIfExists('proveedores');
        Schema::dropIfExists('catalogo_insumos');
    }
};
