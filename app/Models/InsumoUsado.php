<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Detalle del gasto de un producto específico durante una labor.
 */
class InsumoUsado extends Model
{
    use HasFactory;

    protected $table = 'insumos_usados';

    protected $fillable = [
        'labor_id',
        'catalogo_insumo_id',
        'proveedor_id',
        'cantidad',
        'costo_unitario',
        'costo_flete',
        'nombre_proveedor_manual',
    ];

    public function labor()
    {
        return $this->belongsTo(Labor::class, 'labor_id');
    }

    public function producto()
    {
        return $this->belongsTo(CatalogoInsumo::class, 'catalogo_insumo_id');
    }

    public function detalleCatalogo()
    {
        return $this->belongsTo(CatalogoInsumo::class, 'catalogo_insumo_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }
}
