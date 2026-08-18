<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Mensajes globales informativos o de advertencia para todos los usuarios.
 */
class AlertaSistema extends Model
{
    use HasFactory;

    protected $table = 'alertas_sistema';

    protected $fillable = [
        'titulo',
        'mensaje',
        'tipo_alerta',
        'fecha_inicio',
        'fecha_fin',
        'visible',
        'created_by_usuario_id',
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by_usuario_id');
    }
}
