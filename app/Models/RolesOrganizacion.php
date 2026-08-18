<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Catálogo interno de cargos que un usuario puede ejercer dentro de una organización.
 * Ejemplos: Administrador, Supervisor, Agricultor Grado 1.
 */
class RolesOrganizacion extends Model
{
    use HasFactory;

    protected $table = 'roles_organizacion';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /**
     * Relación: Un cargo puede estar asignado a muchos miembros.
     */
    public function miembros()
    {
        return $this->hasMany(MiembroRol::class, 'rol_id');
    }
}
