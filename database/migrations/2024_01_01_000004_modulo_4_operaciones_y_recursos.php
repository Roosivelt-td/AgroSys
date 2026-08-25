<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * MODULO 4: Operaciones y Recursos
     * Gestiona las actividades de campo y el uso de insumos/personal.
     */
    public function up(): void
    {
        // 06. catalogo_labores: Diccionario de tipos de actividades agrícolas
        Schema::create('catalogo_labores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->enum('categoria', ['preparacion', 'siembra', 'mantenimiento', 'cosecha'])->default('mantenimiento');
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        // 17. labores: Registro de una actividad ejecutada sobre un cultivo
        Schema::create('labores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cultivo_id')->constrained('cultivos')->onDelete('cascade');
            $table->foreignId('catalogo_labor_id')->constrained('catalogo_labores');
            $table->date('fecha_realizacion');
            $table->decimal('costo_mano_obra_total', 12, 2)->default(0);
            $table->decimal('costo_maquinaria_total', 12, 2)->default(0);
            $table->decimal('costo_total', 12, 2)->default(0);
            $table->enum('estado', ['Completada', 'Pendiente', 'En progreso'])->default('Pendiente');
            $table->text('observaciones')->nullable();
            $table->string('foto_path', 255)->nullable();
            $table->timestamps();
        });

        // 19. mano_obra_tipo: Clasificación del personal (Jornalero, Operador, etc)
        Schema::create('mano_obra_tipo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->timestamps();
        });

        // 18. mano_obra: Detalle del personal que participo en una labor
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

        // 05. catalogo_insumos: Diccionario maestro de fertilizantes y plaguicidas
        Schema::create('catalogo_insumos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150)->unique();
            $table->string('categoria', 50)->nullable();
            $table->string('unidad_medida', 20)->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('sincronizado')->default(false);
            $table->timestamps();
        });

        // 16. insumos_usados: Detalle de productos consumidos en una labor
        Schema::create('insumos_usados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('labor_id')->constrained('labores')->onDelete('cascade');
            $table->foreignId('catalogo_insumo_id')->constrained('catalogo_insumos');
            $table->foreignId('proveedor_id')->nullable(); // Relacion con proveedores
            $table->decimal('cantidad', 12, 2);
            $table->decimal('costo_unitario', 12, 2);
            $table->decimal('costo_flete', 12, 2)->default(0);
            $table->string('nombre_proveedor_manual', 200)->nullable();
            $table->timestamps();
        });

        // 20. maquinaria_usada: Detalle del uso de maquinaria pesada o herramientas
        Schema::create('maquinaria_usada', function (Blueprint $table) {
            $table->id();
            $table->foreignId('labor_id')->constrained('labores')->onDelete('cascade');
            $table->string('nombre_maquinaria', 150);
            $table->string('labor_realizada', 100)->comment('arar, rastrar, surcar, etc.');
            $table->decimal('horas_trabajadas', 8, 2);
            $table->decimal('costo_total', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maquinaria_usada');
        Schema::dropIfExists('insumos_usados');
        Schema::dropIfExists('catalogo_insumos');
        Schema::dropIfExists('mano_obra');
        Schema::dropIfExists('mano_obra_tipo');
        Schema::dropIfExists('labores');
        Schema::dropIfExists('catalogo_labores');
    }
};
