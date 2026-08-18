<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Clientes o empresas que adquieren la producción de la organización.
 */
class Comprador extends Model
{
    use HasFactory;

    protected $table = 'compradores';

    protected $fillable = [
        'nombre',
        'ruc_dni',
        'telefono',
        'email',
        'direccion',
    ];

    public function comprasRealizadas()
    {
        return $this->hasMany(Venta::class, 'comprador_id');
    }
}
