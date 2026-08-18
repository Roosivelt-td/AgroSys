<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Catálogo de productos químicos o biológicos (Fertilizantes, Plaguicidas).
 */
class CatalogoInsumo extends Model
{
    use HasFactory;

    protected $table = 'catalogo_insumos';

    protected $fillable = [
        'nombre',
        'categoria',
        'unidad_medida',
        'descripcion',
        'sincronizado',
    ];

    public function usos()
    {
        return $this->hasMany(InsumoUsado::class, 'catalogo_insumo_id');
    }
}
