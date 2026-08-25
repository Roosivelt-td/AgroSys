<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * MODULO 3: Producción Agrícola
     * El nucleo operativo del campo.
     */
    public function up(): void
    {
        // 33. terrenos: Registro de parcelas físicas y su geolocalización
        Schema::create('terrenos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones')->onDelete('set null');
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->string('nombre', 150);
            $table->text('ubicacion')->nullable();
            $table->text('direccion_referencia')->nullable();
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->json('poligono')->nullable();
            $table->decimal('hectareas', 10, 2)->default(0);
            $table->enum('tipo_tenencia', ['propio', 'alquilado'])->default('propio');
            $table->decimal('costo_alquiler_anual', 12, 2)->default(0);
            $table->enum('alquiler_modalidad', ['global', 'por_campana'])->default('global');
            $table->enum('alquiler_periodo', ['fecha', 'anual'])->default('fecha');
            $table->date('fecha_alquiler')->nullable();
            $table->date('fecha_vencimiento_alquiler')->nullable();
            $table->enum('calidad_suelo', ['arcilloso', 'franco', 'arenoso', 'limoso'])->default('franco');
            $table->string('fuente_agua', 50)->nullable();
            $table->enum('estado_terreno', ['activo', 'inactivo'])->default('activo');
            $table->string('foto_path', 255)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        // 04. catalogo_cultivos: Diccionario maestro de plantas y parámetros técnicos
        Schema::create('catalogo_cultivos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->string('nombre_cientifico', 150)->nullable();
            $table->enum('tipo_ciclo', ['ciclo_corto', 'perenne']);
            $table->integer('vida_util_estimada_meses')->nullable();
            $table->integer('dias_a_cosecha_promedio')->nullable();
            $table->text('instrucciones_base_riego')->nullable();
            $table->text('instrucciones_base_plagas')->nullable();
            $table->string('foto_path', 255)->nullable();
            $table->tinyInteger('es_personalizado')->default(0);
            $table->foreignId('usuario_creador_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamps();
        });

        // 13. cultivos: Campañas de siembra específicas en un terreno
        Schema::create('cultivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('terreno_id')->constrained('terrenos');
            $table->foreignId('catalogo_cultivo_id')->constrained('catalogo_cultivos');
            $table->string('nombre_lote', 100);
            $table->string('variedad', 100)->nullable();
            $table->date('fecha_planificada');
            $table->date('fecha_siembra');
            $table->date('fecha_cosecha_estimada')->nullable();
            $table->date('fecha_cosecha_finalizada')->nullable();
            $table->enum('estado', ['Planificado', 'En crecimiento', 'Cosechado', 'Perdido'])->default('Planificado');
            $table->decimal('area_destinada', 10, 2)->nullable();
            $table->integer('plantas_estimadas')->nullable();
            $table->decimal('rendimiento_esperado_tn_ha', 10, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->string('foto_path', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cultivos');
        Schema::dropIfExists('catalogo_cultivos');
        Schema::dropIfExists('terrenos');
    }
};
