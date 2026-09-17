<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClimaPronostico extends Model
{
    use HasFactory;

    protected $table = 'clima_pronosticos';

    protected $fillable = [
        'terreno_id',
        'fecha',
        'temp_max',
        'temp_min',
        'prob_lluvia',
        'condicion',
        'icon',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function terreno()
    {
        return $this->belongsTo(Terreno::class, 'terreno_id');
    }
}
