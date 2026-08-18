<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Captura de datos meteorológicos asociados a un terreno específico.
 */
class ClimaRegistro extends Model
{
    use HasFactory;

    protected $table = 'clima_registros';

    protected $fillable = [
        'terreno_id',
        'fecha_hora',
        'temperatura',
        'humedad',
        'viento_kmh',
        'presion_hpa',
        'prob_lluvia',
        'precipitacion_mm',
        'condicion',
    ];

    public function terreno()
    {
        return $this->belongsTo(Terreno::class, 'terreno_id');
    }
}
