<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Órdenes o sugerencias de trabajo enviadas de un Supervisor a un Agricultor.
 */
class SugerenciaTarea extends Model
{
    use HasFactory;

    protected $table = 'sugerencias_tareas';

    protected $fillable = [
        'organizacion_id',
        'supervisor_usuario_id',
        'agricultor_usuario_id',
        'cultivo_id',
        'titulo',
        'descripcion',
        'fecha_sugerida',
        'estado',
        'fecha_respuesta',
        'comentario_agricultor',
    ];

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_usuario_id');
    }

    public function agricultor()
    {
        return $this->belongsTo(User::class, 'agricultor_usuario_id');
    }

    public function cultivo()
    {
        return $this->belongsTo(Cultivo::class, 'cultivo_id');
    }
}
