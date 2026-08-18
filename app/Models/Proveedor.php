<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Entidades externas que venden insumos o prestan servicios.
 */
class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre_empresa',
        'ruc',
        'telefono',
        'tipo_servicio',
    ];

    public function insumosSuministrados()
    {
        return $this->hasMany(InsumoUsado::class, 'proveedor_id');
    }
}
