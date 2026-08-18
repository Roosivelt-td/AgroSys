<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Vincula usuarios a hilos de chat específicos.
 */
class ConversacionParticipante extends Model
{
    use HasFactory;

    protected $table = 'conversaciones_participantes';

    protected $fillable = [
        'conversacion_id',
        'usuario_id',
        'ultima_lectura',
        'limpiado_at',
        'abandonado',
    ];

    public function conversacion()
    {
        return $this->belongsTo(Conversacion::class, 'conversacion_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
