<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\Terreno;
use App\Models\Cultivo;
use App\Models\Labor;
use App\Models\Cosecha;
use App\Models\Venta;
use App\Models\ClimaRegistro;
use App\Models\InsumoUsado;

class AgroTools
{
    public static function getTerrenos(User $user) {
        return Terreno::where('usuario_id', $user->id)->get(['nombre', 'hectareas', 'calidad_suelo', 'fuente_agua', 'ubicacion'])->toArray();
    }

    public static function getCultivos(User $user) {
        return Cultivo::whereHas('terreno', fn($q) => $q->where('usuario_id', $user->id))
            ->with('detalleCatalogo')->get()->map(fn($c) => [
                'cultivo' => $c->detalleCatalogo->nombre,
                'variedad' => $c->variedad,
                'estado' => $c->estado,
                'siembra' => $c->fecha_siembra,
                'terreno' => $c->terreno->nombre
            ])->toArray();
    }

    public static function getLaboresRecientes(User $user) {
        return Labor::whereHas('cultivo.terreno', fn($q) => $q->where('usuario_id', $user->id))
            ->with(['detalleCatalogo', 'cultivo'])->latest()->take(10)->get()->map(fn($l) => [
                'actividad' => $l->detalleCatalogo->nombre,
                'fecha' => $l->fecha_realizacion,
                'cultivo' => $l->cultivo->nombre_lote,
                'costo' => $l->costo_total,
                'notas' => $l->observaciones
            ])->toArray();
    }

    public static function getInsumosUsados(User $user) {
        return InsumoUsado::whereHas('labor.cultivo.terreno', fn($q) => $q->where('usuario_id', $user->id))
            ->with(['catalogoInsumo', 'labor.detalleCatalogo'])->latest()->take(10)->get()->map(fn($i) => [
                'insumo' => $i->catalogoInsumo->nombre,
                'cantidad' => $i->cantidad_utilizada . " " . $i->unidad_medida,
                'labor' => $i->labor->detalleCatalogo->nombre,
                'fecha' => $i->created_at->format('d-m-Y')
            ])->toArray();
    }

    public static function getClimaHistorico(User $user) {
        return ClimaRegistro::whereHas('terreno', fn($q) => $q->where('usuario_id', $user->id))
            ->latest()->take(5)->get(['temp_max', 'temp_min', 'humedad', 'precipitacion_mm', 'fecha_registro'])->toArray();
    }

    public static function getResumenFinanciero(User $user) {
        $ventas = Venta::whereHas('cosecha.labor.cultivo.terreno', fn($q) => $q->where('usuario_id', $user->id))->get();
        return [
            'total_ventas' => $ventas->sum('monto_total'),
            'cantidad_operaciones' => $ventas->count(),
            'ultimas_ventas' => $ventas->take(3)->map(fn($v) => "S/ " . $v->monto_total . " el " . $v->fecha_venta)->toArray()
        ];
    }
}
