<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo que representa a las empresas, cooperativas o fundos agrícolas.
 * Es el núcleo del modelo Multi-empresa (SaaS). Casi toda la información
 * del sistema orbitará alrededor de un ID de esta tabla.
 */
class Organizacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'organizaciones';

    protected $fillable = [
        'nombre',
        'descripcion',
        'ruc',
        'telefono',
        'email',
        'direccion',
        'estado',
    ];

    /**
     * Relación: Una organización tiene muchos miembros (usuarios vinculados).
     */
    public function miembros()
    {
        return $this->hasMany(MiembroOrganizacion::class, 'organizacion_id');
    }

    /**
     * Relación: Una organización tiene muchos terrenos.
     */
    public function terrenos()
    {
        return $this->hasMany(Terreno::class, 'organizacion_id');
    }

    /**
     * Relación: Una organización tiene muchos eventos (reuniones, capacitaciones).
     */
    public function eventos()
    {
        return $this->hasMany(EventoOrganizacion::class, 'organizacion_id');
    }
}
