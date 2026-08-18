<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que asigna cargos específicos a un miembro de una organización.
 * Permite que un usuario tenga múltiples funciones activas o inactivas.
 */
class MiembroRol extends Model
{
    use HasFactory;

    protected $table = 'miembro_roles';

    protected $fillable = [
        'miembro_id',
        'rol_id',
        'estado',
    ];

    /**
     * Relación: Obtener el registro de membresía (usuario-empresa) al que se asignó el cargo.
     */
    public function miembro()
    {
        return $this->belongsTo(MiembroOrganizacion::class, 'miembro_id');
    }

    /**
     * Relación: Obtener la definición del cargo asignado.
     */
    public function rolDetalle()
    {
        return $this->belongsTo(RolesOrganizacion::class, 'rol_id');
    }
}
