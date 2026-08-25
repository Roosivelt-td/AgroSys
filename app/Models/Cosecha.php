<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Registro de la producción obtenida tras finalizar un ciclo de cultivo.
 */
class Cosecha extends Model
{
    use HasFactory;

    protected $table = 'cosechas';

    protected $fillable = [
        'labor_id',
        'fecha_cosecha',
        'cantidad_kg',
        'unidad_medida',
        'calidad',
        'lote_codigo',
        'costo_operativo_cosecha',
        'observaciones',
        'foto_path',
    ];

    protected $casts = [
        'fecha_cosecha' => 'date',
    ];

    /**
     * Relación: La labor de tipo 'cosecha' que generó este registro.
     */
    public function labor()
    {
        return $this->belongsTo(Labor::class, 'labor_id');
    }

    /**
     * Relación: Ventas realizadas a partir de esta cosecha.
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'cosecha_id');
    }
}
