<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo que representa las parcelas físicas de tierra.
 * Contiene información técnica del suelo, ubicación y tenencia.
 */
class Terreno extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'terrenos';

    protected $fillable = [
        'organizacion_id',
        'usuario_id',
        'nombre',
        'ubicacion',
        'direccion_referencia',
        'latitud',
        'longitud',
        'poligono',
        'hectareas',
        'tipo_tenencia',
        'costo_alquiler_anual',
        'alquiler_modalidad',
        'alquiler_periodo',
        'fecha_alquiler',
        'fecha_vencimiento_alquiler',
        'calidad_suelo',
        'fuente_agua',
        'estado_terreno',
        'foto_path',
        'estado',
    ];

    protected $casts = [
        'fecha_alquiler' => 'date',
        'fecha_vencimiento_alquiler' => 'date',
        'poligono' => 'array',
    ];

    /**
     * Relación: Un terreno pertenece a una organización.
     */
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    /**
     * Relación: Un terreno tiene un responsable directo (Usuario).
     */
    public function responsable()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación: Un terreno puede tener varios cultivos a lo largo del tiempo.
     */
    public function cultivos()
    {
        return $this->hasMany(Cultivo::class, 'terreno_id');
    }

    /**
     * Relación: Registros climáticos asociados a este terreno específico.
     */
    public function registrosClima()
    {
        return $this->hasMany(ClimaRegistro::class, 'terreno_id');
    }

    /**
     * Relación: El registro climático más reciente para este terreno.
     */
    public function latestClima()
    {
        return $this->hasOne(ClimaRegistro::class, 'terreno_id')->latestOfMany('fecha_hora');
    }

    /**
     * Área ocupada por cultivos activos.
     */
    public function getAreaOcupadaAttribute()
    {
        return $this->cultivos()->whereIn('estado', ['Planificado', 'En crecimiento'])->sum('area_destinada');
    }

    /**
     * Verifica si el contrato de alquiler ha finalizado (por fecha o por fin de campaña).
     */
    public function getIsAlquilerVencidoAttribute(): bool
    {
        if ($this->tipo_tenencia !== 'alquilado') {
            return false;
        }

        // 1. Por Fecha de Vencimiento de Contrato
        if ($this->fecha_vencimiento_alquiler && $this->fecha_vencimiento_alquiler->isPast()) {
            return true;
        }

        // 2. Por Modalidad Campaña / Cosecha Finalizada:
        if ($this->alquiler_modalidad === 'por_campana' || $this->alquiler_modalidad === 'campana' || $this->alquiler_modalidad === 'cosecha') {
            $hasCultivos = $this->cultivos()->exists();
            $activeCultivos = $this->cultivos()->whereIn('estado', ['Planificado', 'En crecimiento'])->exists();
            if ($hasCultivos && !$activeCultivos) {
                return true;
            }
        }

        return false;
    }

    /**
     * Indica si el terreno está habilitado para recibir nuevos cultivos.
     */
    public function getIsHabilitadoParaCultivoAttribute(): bool
    {
        if ($this->estado_terreno === 'inactivo' || $this->estado === 0) {
            return false;
        }

        if ($this->is_alquiler_vencido) {
            return false;
        }

        return $this->area_disponible > 0;
    }

    /**
     * Área disponible para nuevos cultivos.
     */
    public function getAreaDisponibleAttribute()
    {
        if ($this->is_alquiler_vencido) {
            return 0; // Si el alquiler finalizó, no tiene área disponible para nuevos cultivos
        }

        return max(0, $this->hectareas - $this->area_ocupada);
    }
}
