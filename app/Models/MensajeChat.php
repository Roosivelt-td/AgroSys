<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Mensaje individual dentro de un chat. Soporta archivos adjuntos e IA.
 */
class MensajeChat extends Model
{
    use HasFactory;

    protected $table = 'mensajes_chat';

    protected $fillable = [
        'conversacion_id',
        'remitente_usuario_id',
        'es_ia',
        'mensaje',
        'archivo_adjunto_id',
        'leido',
        'editado_at',
        'eliminado_todos'
    ];

    /**
     * Usuarios que han borrado este mensaje para sí mismos.
     */
    public function borradoPorUsuarios()
    {
        return $this->belongsToMany(User::class, 'mensajes_borrados_usuario', 'mensaje_id', 'usuario_id');
    }

    public function conversacion()
    {
        return $this->belongsTo(Conversacion::class, 'conversacion_id');
    }

    public function remitente()
    {
        return $this->belongsTo(User::class, 'remitente_usuario_id');
    }

    public function adjunto()
    {
        return $this->belongsTo(ArchivoMultimedia::class, 'archivo_adjunto_id');
    }
}
