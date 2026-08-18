<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Repositorio central de archivos vinculados a usuarios y organizaciones.
 */
class ArchivoMultimedia extends Model
{
    use HasFactory;

    protected $table = 'archivos_multimedia';

    protected $fillable = [
        'usuario_id',
        'organizacion_id',
        'nombre_original',
        'nombre_archivo_unique',
        'ruta_completa',
        'tipo_mime',
        'tamano_bytes',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }
}
