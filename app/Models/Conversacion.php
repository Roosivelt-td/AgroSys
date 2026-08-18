<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Contenedor de hilos de chat (Privado, Grupal o con IA).
 */
class Conversacion extends Model
{
    use HasFactory;

    protected $table = 'conversaciones';

    protected $fillable = [
        'organizacion_id',
        'tipo_conversacion',
        'nombre_grupo',
    ];

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    public function participantes()
    {
        return $this->hasMany(ConversacionParticipante::class, 'conversacion_id');
    }

    public function mensajes()
    {
        return $this->hasMany(MensajeChat::class, 'conversacion_id');
    }
}
