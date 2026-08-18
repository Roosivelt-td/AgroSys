<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo que resuelve la relación muchos a muchos entre Usuarios y Organizaciones.
 * Define si un usuario pertenece a una empresa específica.
 */
class MiembroOrganizacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'miembros_organizacion';

    protected $fillable = [
        'usuario_id',
        'organizacion_id',
        'es_propietario',
        'estado',
        'fecha_ingreso',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
    ];

    /**
     * Relación: Obtener el usuario al que pertenece este registro de membresía.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación: Obtener la organización a la que pertenece este miembro.
     */
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    /**
     * Relación: Un miembro puede tener varios roles/cargos dentro de la organización
     * (Ej: Agricultor, Supervisor, Administrador). Máximo 3 según lógica de negocio.
     */
    public function roles()
    {
        return $this->hasMany(MiembroRol::class, 'miembro_id');
    }
}
