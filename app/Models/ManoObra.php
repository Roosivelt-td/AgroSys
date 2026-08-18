<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Detalle del costo humano aplicado a una labor agrícola.
 */
class ManoObra extends Model
{
    use HasFactory;

    protected $table = 'mano_obra';

    protected $fillable = [
        'labor_id',
        'tipo_id',
        'cantidad_trabajadores',
        'dias_trabajados',
        'costo_por_dia',
        'subtotal',
    ];

    public function labor()
    {
        return $this->belongsTo(Labor::class, 'labor_id');
    }

    public function tipoPersona()
    {
        return $this->belongsTo(ManoObraTipo::class, 'tipo_id');
    }
}
