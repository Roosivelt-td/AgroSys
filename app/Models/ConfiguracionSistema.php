<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Parámetros de identidad visual y configuración global de la plataforma.
 */
class ConfiguracionSistema extends Model
{
    use HasFactory;

    protected $table = 'configuracion_sistema';

    protected $fillable = [
        'nombre_sistema',
        'lema_sistema',
        'version_sistema',
        'descripcion_detallada',
        'logo_path',
        'favicon_path',
        'color_primario',
        'color_secundario',
        'updated_by_usuario_id',
    ];

    public function ultimoEditor()
    {
        return $this->belongsTo(User::class, 'updated_by_usuario_id');
    }
}
