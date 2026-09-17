<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Representa una siembra específica en un terreno.
 * Es la unidad principal de seguimiento productivo.
 */
class Cultivo extends Model
{
    use HasFactory;

    protected $table = 'cultivos';

    protected $fillable = [
        'terreno_id',
        'catalogo_cultivo_id',
        'nombre_lote',
        'variedad',
        'fecha_planificada',
        'fecha_siembra',
        'fecha_cosecha_estimada',
        'fecha_cosecha_finalizada',
        'estado',
        'area_destinada',
        'plantas_estimadas',
        'rendimiento_esperado_tn_ha',
        'observaciones',
        'foto_path',
    ];

    protected $casts = [
        'fecha_planificada' => 'date',
        'fecha_siembra' => 'date',
        'fecha_cosecha_estimada' => 'date',
        'fecha_cosecha_finalizada' => 'date',
    ];

    /**
     * Relación: En qué terreno se encuentra sembrado.
     */
    public function terreno()
    {
        return $this->belongsTo(Terreno::class, 'terreno_id');
    }

    /**
     * Relación: Qué planta es según el catálogo.
     */
    public function detalleCatalogo()
    {
        return $this->belongsTo(CatalogoCultivo::class, 'catalogo_cultivo_id');
    }

    /**
     * Obtiene una descripción legible y completa del cultivo para banners y reportes.
     */
    public function getDescripcionCompletaAttribute()
    {
        $area = number_format($this->area_destinada, 2);
        $cultivo = strtoupper($this->detalleCatalogo->nombre);
        $variedad = strtoupper($this->variedad ?: 'GENERICA');
        $tipoFecha = ($this->estado === 'Planificado') ? 'PLANIFICADO' : 'SEMBRADO';
        $fecha = ($this->estado === 'Planificado')
            ? ($this->fecha_planificada ? $this->fecha_planificada->format('d/m/Y') : '---')
            : ($this->fecha_siembra ? $this->fecha_siembra->format('d/m/Y') : '---');
        $terreno = strtoupper($this->terreno->nombre);

        return "{$area} HA DE {$cultivo} {$variedad}, {$tipoFecha} EL {$fecha} EN EL TERRENO {$terreno}";
    }

    /**
     * Relación: Actividades realizadas a este cultivo (Riego, Abono, etc.).
     */
    public function labores()
    {
        return $this->hasMany(Labor::class, 'cultivo_id');
    }

    /**
     * Relación: Cosechas obtenidas a partir de las labores de este cultivo.
     */
    public function cosechas()
    {
        return $this->hasManyThrough(Cosecha::class, Labor::class, 'cultivo_id', 'labor_id');
    }
}
