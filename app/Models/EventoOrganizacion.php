<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Reuniones o avisos generales para todos los miembros de una empresa.
 */
class EventoOrganizacion extends Model
{
    use HasFactory;

    protected $table = 'eventos_organizacion';

    protected $fillable = [
        'organizacion_id',
        'creado_por_usuario_id',
        'titulo',
        'descripcion',
        'fecha_evento',
        'duracion_minutos',
        'lugar',
        'enlace_virtual',
    ];

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por_usuario_id');
    }
}
