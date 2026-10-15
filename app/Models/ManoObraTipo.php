<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Clasificación del personal (Ej: Jornalero, Técnico, Operador).
 */
class ManoObraTipo extends Model
{
    use HasFactory;

    protected $table = 'mano_obra_tipo';

    protected $fillable = ['nombre'];

    /**
     * Garantiza que siempre existan los tipos de mano de obra base en el sistema.
     */
    public static function garantizarTiposBase()
    {
        $tipos = static::all();
        if ($tipos->isEmpty()) {
            $defaultNames = ['Jornalero', 'Operador de Maquinaria', 'Especialista Técnico'];
            foreach ($defaultNames as $nombre) {
                static::firstOrCreate(['nombre' => $nombre]);
            }
            $tipos = static::all();
        }
        return $tipos;
    }

    public function registrosManoObra()
    {
        return $this->hasMany(ManoObra::class, 'tipo_id');
    }
}
