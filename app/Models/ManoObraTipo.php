<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Clasificación del personal (Ej: Jornalero, Técnico, Operador).
 */
class ManoObraTipo extends Model
{
    use HasFactory;

    protected $table = 'mano_obra_tipo';

    protected $fillable = ['nombre'];

    public function registrosManoObra()
    {
        return $this->hasMany(ManoObra::class, 'tipo_id');
    }
}
