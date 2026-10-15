<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoUsuarioOculto extends Model
{
    use HasFactory;

    protected $table = 'eventos_ocultos_usuario';

    protected $fillable = [
        'evento_id',
        'usuario_id',
    ];

    public function evento()
    {
        return $this->belongsTo(EventoOrganizacion::class, 'evento_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
