<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usuarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'rol_id',
        'nombres',
        'apellidos',
        'email',
        'password',
        'telefono',
        'dni',
        'estado',
        'experiencia_anios',
        'nivel_educativo',
        'ubicacion',
        'descripcion',
        'foto_perfil_url',
        'foto_portada_url',
        'is_activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_activo' => 'boolean',
        ];
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    /**
     * Relación: Un usuario puede pertenecer a varias organizaciones (Multi-empresa).
     */
    public function membresias()
    {
        return $this->hasMany(MiembroOrganizacion::class, 'usuario_id');
    }

    /**
     * Relación: Si el usuario es agricultor, puede tener supervisores asignados.
     */
    public function misSupervisores()
    {
        return $this->hasMany(AsignacionSupervisor::class, 'agricultor_usuario_id');
    }

    /**
     * Relación: Obtener el rol global del usuario (Super Admin o Agricultor).
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    /**
     * Obtiene el nombre del rol a mostrar en la interfaz.
     * Prioriza roles de organización sobre el rol global de agricultor.
     */
    public function getDisplayRoleAttribute()
    {
        if ($this->rol_id === 1) {
            return 'Super Admin';
        }

        // Buscar si tiene cargo de Administrador ACTIVO en alguna organización ACTIVA
        $esAdmin = $this->membresias()->where('estado', 1)->whereHas('roles', function ($query) {
            $query->where('estado', 1)->whereHas('rolDetalle', function($q) {
                $q->where('nombre', 'Administrador');
            });
        })->exists();

        if ($esAdmin) {
            return 'Administrador';
        }

        // Buscar si tiene cargo de Supervisor ACTIVO en alguna organización ACTIVA
        $esSupervisor = $this->membresias()->where('estado', 1)->whereHas('roles', function ($query) {
            $query->where('estado', 1)->whereHas('rolDetalle', function($q) {
                $q->where('nombre', 'Supervisor');
            });
        })->exists();

        if ($esSupervisor) {
            return 'Supervisor';
        }

        return 'Agricultor';
    }

    /**
     * Verifica si el usuario es miembro de una organización específica.
     */
    public function esMiembroDe($organizacionId)
    {
        return $this->membresias()->where('organizacion_id', $organizacionId)->where('estado', 1)->exists();
    }

    /**
     * Verifica si el usuario tiene un rol específico ACTIVO en al menos una organización ACTIVA.
     */
    public function tieneRolEnCualquierOrganizacion($nombreRol)
    {
        return $this->membresias()->where('estado', 1)->whereHas('roles', function ($query) use ($nombreRol) {
            $query->where('estado', 1)->whereHas('rolDetalle', function($q) use ($nombreRol) {
                $q->where('nombre', $nombreRol);
            });
        })->exists();
    }

    /**
     * Verifica si el usuario es Administrador de alguna organización.
     */
    public function esAdminDeOrganizacion()
    {
        return $this->tieneRolEnCualquierOrganizacion('Administrador');
    }

    /**
     * Verifica si el usuario es Supervisor de alguna organización.
     */
    public function esSupervisorDeOrganizacion()
    {
        return $this->tieneRolEnCualquierOrganizacion('Supervisor');
    }
}
