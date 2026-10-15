<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Catálogo maestro de tipos de plantas (Papa, Maíz, etc.) con sus
 * parámetros técnicos ideales y rangos de rendimiento por Ha.
 */
class CatalogoCultivo extends Model
{
    use HasFactory;

    protected $table = 'catalogo_cultivos';

    protected $fillable = [
        'nombre',
        'nombre_cientifico',
        'tipo_ciclo',
        'vida_util_estimada_meses',
        'dias_a_cosecha_promedio',
        'rendimiento_min_tn_ha',
        'rendimiento_max_tn_ha',
        'instrucciones_base_riego',
        'instrucciones_base_plagas',
        'foto_path',
        'es_personalizado',
        'usuario_creador_id',
    ];

    /**
     * Formatea números recortando ceros decimales innecesarios (ej. 1.00 -> 1 TN, 1.50 -> 1.5 TN).
     */
    public static function formatTnNumber($value): string
    {
        $num = (float)$value;
        $formatted = number_format($num, 2, '.', '');
        $trimmed = rtrim(rtrim($formatted, '0'), '.');
        return $trimmed === '' ? '0' : $trimmed;
    }

    /**
     * Rango de rendimiento base formateado por 1 Ha (ej. 3 TN A 4 TN / 1 Ha).
     */
    public function getRangoRendimientoFormattedAttribute(): string
    {
        $min = self::formatTnNumber($this->rendimiento_min_tn_ha ?: 5);
        $max = self::formatTnNumber($this->rendimiento_max_tn_ha ?: 6);
        return "{$min} TN A {$max} TN / 1 Ha";
    }

    /**
     * Calcula el rango de rendimiento esperado según el área a sembrar en hectáreas.
     */
    public function calcularRangoRendimientoHectareas($hectareas): array
    {
        $ha = max(0.01, (float)$hectareas);
        $minBase = (float)($this->rendimiento_min_tn_ha ?: 5.0);
        $maxBase = (float)($this->rendimiento_max_tn_ha ?: 6.0);

        $minCalculado = round($minBase * $ha, 2);
        $maxCalculado = round($maxBase * $ha, 2);
        $promedioPerHa = round(($minBase + $maxBase) / 2, 2);
        $promedioTotal = round(($minCalculado + $maxCalculado) / 2, 2);

        $minFmt = self::formatTnNumber($minCalculado);
        $maxFmt = self::formatTnNumber($maxCalculado);

        return [
            'min_tn' => $minCalculado,
            'max_tn' => $maxCalculado,
            'promedio_per_ha' => $promedioPerHa,
            'promedio_total' => $promedioTotal,
            'label' => "{$minFmt} TN A {$maxFmt} TN"
        ];
    }

    /**
     * Relación: Quién sugirió o creó este cultivo en el catálogo (si es personalizado).
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'usuario_creador_id');
    }

    /**
     * Relación: Instancias reales de siembra basadas en este catálogo.
     */
    public function cultivosRealizados()
    {
        return $this->hasMany(Cultivo::class, 'catalogo_cultivo_id');
    }
}
