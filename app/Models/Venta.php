<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Registro de la comercialización de los productos cosechados.
 */
class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'cosecha_id',
        'comprador_id',
        'fecha_venta',
        'cantidad_vendida_kg',
        'precio_por_kg',
        'costo_flete',
        'impuestos',
        'comprobante_tipo',
        'comprobante_numero',
        'foto_path',
    ];

    protected $casts = [
        'fecha_venta' => 'date',
    ];

    public function cosecha()
    {
        return $this->belongsTo(Cosecha::class, 'cosecha_id');
    }

    public function comprador()
    {
        return $this->belongsTo(Comprador::class, 'comprador_id');
    }
}
