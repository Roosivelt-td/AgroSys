<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaquinariaUsada extends Model
{
    use HasFactory;

    protected $table = 'maquinaria_usada';

    protected $fillable = [
        'labor_id',
        'nombre_maquinaria',
        'labor_realizada',
        'horas_trabajadas',
        'costo_total',
    ];

    public function labor()
    {
        return $this->belongsTo(Labor::class, 'labor_id');
    }
}
