<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Representa una actividad de campo realizada a un cultivo.
 * Acumula costos de mano de obra e insumos.
 */
class Labor extends Model
{
    use HasFactory;

    protected $table = 'labores';

    protected $fillable = [
        'cultivo_id',
        'catalogo_labor_id',
        'fecha_realizacion',
        'costo_mano_obra_total',
        'costo_maquinaria_total',
        'costo_total',
        'estado',
        'observaciones',
        'foto_path',
    ];

    /**
     * Relación: Cultivo al que se le aplicó la labor.
     */
    public function cultivo()
    {
        return $this->belongsTo(Cultivo::class, 'cultivo_id');
    }

    /**
     * Relación: Qué tipo de labor es (según catálogo).
     */
    public function detalleCatalogo()
    {
        return $this->belongsTo(CatalogoLabor::class, 'catalogo_labor_id');
    }

    /**
     * Relación: Insumos (venenos, abonos) usados en esta labor.
     */
    public function insumos()
    {
        return $this->hasMany(InsumoUsado::class, 'labor_id');
    }

    /**
     * Relación: Personal que trabajó en esta labor.
     */
    public function manoDeObra()
    {
        return $this->hasMany(ManoObra::class, 'labor_id');
    }

    /**
     * Relación: Si la labor fue de tipo 'cosecha', se vincula con la producción obtenida.
     */
    public function cosechaResultado()
    {
        return $this->hasOne(Cosecha::class, 'labor_id');
    }
}
