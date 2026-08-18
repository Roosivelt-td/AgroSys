<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de Seguridad Visual. Define qué agricultores específicos puede ver
 * y gestionar un supervisor dentro de una organización.
 */
class AsignacionSupervisor extends Model
{
    use HasFactory;

    protected $table = 'asignaciones_supervisor';

    protected $fillable = [
        'organizacion_id',
        'supervisor_miembro_id',
        'agricultor_usuario_id',
    ];

    /**
     * Relación: La organización bajo la cual se hace la asignación.
     */
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    /**
     * Relación: El miembro que actúa como supervisor.
     */
    public function supervisor()
    {
        return $this->belongsTo(MiembroOrganizacion::class, 'supervisor_miembro_id');
    }

    /**
     * Relación: El usuario que es supervisado (Agricultor).
     */
    public function agricultor()
    {
        return $this->belongsTo(User::class, 'agricultor_usuario_id');
    }
}
