<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudAccesoSesion extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_acceso_sesion';

    protected $fillable = [
        'usuario_id',
        'session_id_solicitante',
        'ip_solicitante',
        'user_agent_solicitante',
        'dispositivo_solicitante',
        'estado',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
