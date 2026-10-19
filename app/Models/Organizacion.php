<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

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
        'fecha_adquisicion',
        'fecha_vencimiento',
        'anios_suscripcion',
        'monto_pago',
    ];

    protected $casts = [
        'fecha_adquisicion' => 'date',
        'fecha_vencimiento' => 'date',
        'anios_suscripcion' => 'integer',
        'monto_pago' => 'decimal:2',
    ];

    /**
     * Evalúa si la suscripción de la organización ha vencido.
     */
    public function getIsVencidaAttribute()
    {
        if ($this->estado == 0) return true;
        if (!$this->fecha_vencimiento) return false;
        return Carbon::parse($this->fecha_vencimiento)->isPast();
    }

    /**
     * Calcula los días restantes de la suscripción anual.
     */
    public function getDiasRestantesAttribute()
    {
        if (!$this->fecha_vencimiento) return 365;
        return max(0, (int)Carbon::now()->diffInDays(Carbon::parse($this->fecha_vencimiento), false));
    }

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
