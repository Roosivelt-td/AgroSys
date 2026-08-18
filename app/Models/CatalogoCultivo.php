<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Catálogo maestro de tipos de plantas (Papa, Maíz, etc.) con sus
 * parámetros técnicos ideales.
 */
class CatalogoCultivo extends Model
{
    use HasFactory;

    protected $table = 'catalogo_cultivos';

    protected $fillable = [
        'nombre',
        'nombre_cientifico',
        'tipo_ciclo',
        'vida_util_estimada_meses',
        'dias_a_cosecha_promedio',
        'instrucciones_base_riego',
        'instrucciones_base_plagas',
        'foto_path',
        'es_personalizado',
        'usuario_creador_id',
    ];

    /**
     * Relación: Quién sugirió o creó este cultivo en el catálogo (si es personalizado).
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'usuario_creador_id');
    }

    /**
     * Relación: Instancias reales de siembra basadas en este catálogo.
     */
    public function cultivosRealizados()
    {
        return $this->hasMany(Cultivo::class, 'catalogo_cultivo_id');
    }
}
