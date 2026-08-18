<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Catálogo de tipos de actividades agrícolas (Fumigación, Riego, Cosecha).
 */
class CatalogoLabor extends Model
{
    use HasFactory;

    protected $table = 'catalogo_labores';

    protected $fillable = [
        'nombre',
        'categoria',
        'descripcion',
    ];

    /**
     * Relación: Labores reales ejecutadas de este tipo.
     */
    public function laboresRealizadas()
    {
        return $this->hasMany(Labor::class, 'catalogo_labor_id');
    }
}
