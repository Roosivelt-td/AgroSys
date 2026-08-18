<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Enlaces de redes sociales oficiales de la plataforma.
 */
class RedSocialSistema extends Model
{
    use HasFactory;

    protected $table = 'redes_sociales_sistema';

    protected $fillable = [
        'usuario_id',
        'tipo_red_social',
        'url_enlace',
        'icono_path',
        'visible',
        'orden',
    ];

    public function editor()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
