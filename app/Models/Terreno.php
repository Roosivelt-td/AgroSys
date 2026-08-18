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
}
