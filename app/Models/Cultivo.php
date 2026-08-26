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
     * Relación: Actividades realizadas a este cultivo (Riego, Abono, etc.).
     */
    public function labores()
    {
        return $this->hasMany(Labor::class, 'cultivo_id');
    }
}
