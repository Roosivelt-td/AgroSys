<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Registro forense e inmutable de todas las acciones del sistema.
 */
class HistorialProceso extends Model
{
    use HasFactory;

    protected $table = 'historial_procesos';

    protected $fillable = [
        'usuario_id',
        'organizacion_id',
        'tabla_afectada',
        'registro_id',
        'accion',
        'descripcion',
        'detalles_previos',
    ];

    protected $casts = [
        'detalles_previos' => 'array',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id')->withTrashed();
    }

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }
}
