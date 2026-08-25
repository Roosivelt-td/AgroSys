<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * MODULO 5: Comercialización y Clima
     * Maneja las salidas del sistema (ventas) y factores externos (clima).
     */
    public function up(): void
    {
        // 27. proveedores: Empresas que suministran insumos o maquinaria
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_empresa', 200);
            $table->string('ruc', 20)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('tipo_servicio', 50)->nullable();
            $table->timestamps();
        });

        // Vincular insumos_usados con proveedores (Intersección foránea)
        Schema::table('insumos_usados', function (Blueprint $table) {
            $table->foreign('proveedor_id')->references('id')->on('proveedores')->onDelete('set null');
        });

        // 08. compradores: Clientes finales de la producción cosechada
        Schema::create('compradores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200);
            $table->string('ruc_dni', 20)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('direccion')->nullable();
            $table->timestamps();
        });

        // 12. cosechas: Registro de producción obtenida al final de una labor
        Schema::create('cosechas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('labor_id')->nullable()->constrained('labores')->onDelete('set null');
            $table->date('fecha_cosecha');
            $table->decimal('cantidad_kg', 12, 2);
            $table->string('unidad_medida', 20)->default('kg');
            $table->enum('calidad', ['primera', 'segunda', 'descarte'])->default('primera');
            $table->string('lote_codigo', 50)->nullable();
            $table->decimal('costo_operativo_cosecha', 12, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->string('foto_path', 255)->nullable();
            $table->timestamps();
        });

        // 35. ventas: Transacciones comerciales de los productos cosechados
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cosecha_id')->constrained('cosechas');
            $table->foreignId('comprador_id')->constrained('compradores');
            $table->date('fecha_venta');
            $table->decimal('cantidad_vendida_kg', 12, 2);
            $table->decimal('precio_por_kg', 12, 2);
            $table->decimal('costo_flete', 12, 2);
            $table->decimal('impuestos', 12, 2);
            $table->enum('comprobante_tipo', ['boleta', 'factura', 'ticket'])->default('boleta');
            $table->string('comprobante_numero', 50)->nullable();
            $table->string('foto_path', 255)->nullable();
            $table->timestamps();
        });

        // 07. clima_registros: Captura de datos meteorológicos por terreno
        Schema::create('clima_registros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('terreno_id')->constrained('terrenos')->onDelete('cascade');
            $table->timestamp('fecha_hora')->useCurrent();
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
        Schema::table('insumos_usados', function (Blueprint $table) {
            $table->dropForeign(['proveedor_id']);
        });
        Schema::dropIfExists('clima_registros');
        Schema::dropIfExists('ventas');
        Schema::dropIfExists('cosechas');
        Schema::dropIfExists('compradores');
        Schema::dropIfExists('proveedores');
    }
};
