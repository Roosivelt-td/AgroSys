<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Motor de Workflow. Gestiona peticiones entre usuarios y organizaciones.
 */
class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'tipo',
        'estado',
        'solicitante_usuario_id',
        'destinatario_usuario_id',
        'organizacion_id',
        'datos_extra',
        'fecha_solicitud',
        'fecha_respuesta',
    ];

    protected $casts = [
        'datos_extra' => 'array',
        'fecha_solicitud' => 'datetime',
        'fecha_respuesta' => 'datetime',
    ];

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_usuario_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(User::class, 'destinatario_usuario_id');
    }

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'solicitud_id');
    }
}
