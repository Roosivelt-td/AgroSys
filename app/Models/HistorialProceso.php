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

    /**
     * Genera una explicación humana inteligente tipo IA comparando valores anteriores y nuevos.
     */
    public function getExplicacionCambiosAttribute(): array
    {
        $detalles = $this->detalles_previos;
        if (!is_array($detalles)) {
            return [
                'resumen' => $this->descripcion,
                'cambios' => []
            ];
        }

        $anteriores = $detalles['meta_valores_anteriores'] ?? [];
        $nuevos = $detalles['meta_valores_nuevos'] ?? [];

        // Ignorar campos internos de timestamps / tokens / metas
        $ignoredKeys = ['updated_at', 'created_at', 'id', 'remember_token', 'password', 'meta_dispositivo', 'meta_ip', 'meta_valores_anteriores', 'meta_valores_nuevos', 'foto_path', 'foto_perfil_url', 'poligono'];

        $cambiosDetectados = [];

        if (!empty($nuevos) && is_array($nuevos) && !empty($anteriores) && is_array($anteriores)) {
            foreach ($nuevos as $campo => $nuevoValor) {
                if (in_array($campo, $ignoredKeys)) continue;

                $valorAnterior = $anteriores[$campo] ?? null;

                // Si los valores son diferentes
                if (!is_null($valorAnterior) && (string)$valorAnterior !== (string)$nuevoValor) {
                    $nombreCampo = match($campo) {
                        'nombre' => 'Nombre',
                        'hectareas' => 'Área / Hectáreas',
                        'ubicacion' => 'Ubicación',
                        'tipo_tenencia' => 'Tipo de Tenencia',
                        'calidad_suelo' => 'Calidad de Suelo',
                        'fuente_agua' => 'Fuente de Agua',
                        'costo_alquiler_anual' => 'Costo de Alquiler Anual',
                        'estado' => 'Estado',
                        'estado_terreno' => 'Estado del Terreno',
                        'area_destinada' => 'Área Destinada',
                        'plantas_estimadas' => 'Plantas Estimadas',
                        'rendimiento_esperado_tn_ha' => 'Rendimiento Esperado',
                        'variedad' => 'Variedad',
                        'costo_total' => 'Costo Total',
                        'costo_mano_obra_total' => 'Costo Mano de Obra',
                        'costo_maquinaria_total' => 'Costo Maquinaria',
                        'observaciones' => 'Observaciones',
                        'nombres' => 'Nombres',
                        'apellidos' => 'Apellidos',
                        'telefono' => 'Teléfono',
                        'email' => 'Correo Electrónico',
                        'dni' => 'DNI / Documento',
                        'cantidad_kg' => 'Cantidad Cosechada (Kg)',
                        'calidad' => 'Calidad de Producto',
                        'cantidad_vendida_kg' => 'Cantidad Vendida (Kg)',
                        'precio_por_kg' => 'Precio por Kg',
                        'comprador_cliente' => 'Comprador / Cliente',
                        default => mb_convert_case(str_replace('_', ' ', $campo), MB_CASE_TITLE, "UTF-8")
                    };

                    // Formatear valores de forma legible
                    $fmtAnterior = (string)$valorAnterior;
                    $fmtNuevo = (string)$nuevoValor;

                    if (str_contains($campo, 'costo') || str_contains($campo, 'precio')) {
                        $fmtAnterior = 'S/ ' . number_format((float)$valorAnterior, 2);
                        $fmtNuevo = 'S/ ' . number_format((float)$nuevoValor, 2);
                    } elseif ($campo === 'hectareas' || $campo === 'area_destinada') {
                        $fmtAnterior = number_format((float)$valorAnterior, 2) . ' Ha';
                        $fmtNuevo = number_format((float)$nuevoValor, 2) . ' Ha';
                    }

                    $cambiosDetectados[] = [
                        'campo' => $nombreCampo,
                        'anterior' => $fmtAnterior !== '' ? $fmtAnterior : 'Vacío',
                        'nuevo' => $fmtNuevo !== '' ? $fmtNuevo : 'Vacío',
                    ];
                }
            }
        }

        return [
            'resumen' => $this->descripcion,
            'cambios' => $cambiosDetectados
        ];
    }
}
