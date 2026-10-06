<?php

namespace App\Observers;

use App\Models\HistorialProceso;
use App\Models\CatalogoCultivo;
use App\Models\CatalogoLabor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class AgroAuditObserver
{
    public function created(Model $model)
    {
        $this->log($model, 'CREATED');
    }

    public function updated(Model $model)
    {
        if ($model->getTable() === 'historial_procesos') return;

        // Si en un cultivo solo se actualizó la fecha_cosecha_finalizada (operación automática de cosechas), ignorar evento de edición manual
        if ($model->getTable() === 'cultivos' && $model->isDirty('fecha_cosecha_finalizada') && count($model->getDirty()) === 1) {
            return;
        }

        $this->log($model, 'UPDATED');
    }

    public function deleted(Model $model)
    {
        $this->log($model, 'DELETED');
    }

    protected function log(Model $model, string $tipoEvent)
    {
        $userId = Auth::check() ? Auth::id() : (\App\Models\User::value('id'));
        if (!$userId) return;

        $table = $model->getTable();
        $orgId = null;

        if (isset($model->organizacion_id)) {
            $orgId = $model->organizacion_id;
        } elseif (method_exists($model, 'terreno') && $model->terreno) {
            $orgId = $model->terreno->organizacion_id;
        } elseif (method_exists($model, 'cultivo') && $model->cultivo && $model->cultivo->terreno) {
            $orgId = $model->cultivo->terreno->organizacion_id;
        }

        // Custom action and description mapping by table
        if ($table === 'terrenos') {
            $accion = match($tipoEvent) {
                'CREATED' => 'REGISTRO TERRENO',
                'UPDATED' => 'EDICIÓN TERRENO',
                'DELETED' => 'ELIMINACIÓN TERRENO',
            };
            $nombre = $model->nombre ?? 'Terreno';
            $ha = $model->hectareas ?? 0;
            $descripcion = match($tipoEvent) {
                'CREATED' => "Se registró un terreno: {$nombre} ({$ha} Ha)",
                'UPDATED' => "Se editó un terreno: {$nombre} ({$ha} Ha)",
                'DELETED' => "Se eliminó un terreno: {$nombre} ({$ha} Ha)",
            };
        } elseif ($table === 'cultivos') {
            $accion = match($tipoEvent) {
                'CREATED' => 'REGISTRO CULTIVO',
                'UPDATED' => 'EDICIÓN CULTIVO',
                'DELETED' => 'ELIMINACIÓN CULTIVO',
            };
            $cropDetail = CatalogoCultivo::find($model->catalogo_cultivo_id);
            $nombreCultivo = $cropDetail ? $cropDetail->nombre : 'Cultivo';
            $lote = $model->nombre_lote ?? 'Lote';
            $descripcion = match($tipoEvent) {
                'CREATED' => "Se registró un cultivo: {$nombreCultivo} (Lote {$lote})",
                'UPDATED' => "Se editó un cultivo: {$nombreCultivo} (Lote {$lote})",
                'DELETED' => "Se eliminó un cultivo: {$nombreCultivo} (Lote {$lote})",
            };
        } elseif ($table === 'labores') {
            $accion = match($tipoEvent) {
                'CREATED' => 'EJECUCIÓN LABOR',
                'UPDATED' => 'EDICIÓN LABOR',
                'DELETED' => 'ELIMINACIÓN LABOR',
            };
            $laborDetail = CatalogoLabor::find($model->catalogo_labor_id);
            $nombreLabor = $laborDetail ? $laborDetail->nombre : ($model->categoria ?? 'Labor');
            $lote = ($model->cultivo) ? $model->cultivo->nombre_lote : '';
            $nombreCultivo = ($model->cultivo && $model->cultivo->detalleCatalogo) ? $model->cultivo->detalleCatalogo->nombre : '';
            $nombreTerreno = ($model->cultivo && $model->cultivo->terreno) ? $model->cultivo->terreno->nombre : '';

            $contexto = "";
            if ($nombreCultivo && $nombreTerreno) {
                $contexto = " en el cultivo {$nombreCultivo} (Lote {$lote}) del terreno {$nombreTerreno}";
            } elseif ($nombreTerreno) {
                $contexto = " en el terreno {$nombreTerreno}";
            }

            $descripcion = match($tipoEvent) {
                'CREATED' => "Labor de {$nombreLabor} realizada{$contexto}",
                'UPDATED' => "Labor de {$nombreLabor} modificada{$contexto}",
                'DELETED' => "Labor de {$nombreLabor} eliminada{$contexto}",
            };
        } elseif ($table === 'cosechas') {
            $accion = match($tipoEvent) {
                'CREATED' => 'REGISTRO COSECHA',
                'UPDATED' => 'EDICIÓN COSECHA',
                'DELETED' => 'ELIMINACIÓN COSECHA',
            };
            $cantidad = $model->cantidad_kg ?? 0;
            $unidad = $model->unidad_medida ?? 'kg';
            $calidad = $model->calidad ?? 'Estándar';
            $lote = $model->lote_codigo ?? '';
            $descripcion = match($tipoEvent) {
                'CREATED' => "Cosecha registrada: {$cantidad} {$unidad} (Calidad {$calidad})" . ($lote ? " - Lote {$lote}" : ""),
                'UPDATED' => "Cosecha modificada: {$cantidad} {$unidad} (Calidad {$calidad})",
                'DELETED' => "Cosecha de {$cantidad} {$unidad} eliminada",
            };
        } elseif ($table === 'ventas') {
            $accion = match($tipoEvent) {
                'CREATED' => 'REGISTRO VENTA',
                'UPDATED' => 'EDICIÓN VENTA',
                'DELETED' => 'ELIMINACIÓN VENTA',
            };
            $comprador = $model->comprador_cliente ?? 'Cliente';
            $monto = number_format(($model->cantidad_vendida_kg ?? 0) * ($model->precio_por_kg ?? 0), 2);
            $descripcion = match($tipoEvent) {
                'CREATED' => "Venta registrada a {$comprador} por S/ {$monto}",
                'UPDATED' => "Venta a {$comprador} modificada (S/ {$monto})",
                'DELETED' => "Venta realizada a {$comprador} eliminada",
            };
        } else {
            $accion = $tipoEvent;
            $descripcion = "Acción {$tipoEvent} en " . $table;
        }

        $request = request();
        $ip = $request ? ($request->ip() ?: '127.0.0.1') : '127.0.0.1';
        $ua = $request ? ($request->userAgent() ?: 'Navegador') : 'Navegador';

        $os = 'Sistema Desconocido';
        if (preg_match('/windows nt 10/i', $ua)) $os = 'Windows 10/11';
        elseif (preg_match('/windows/i', $ua)) $os = 'Windows';
        elseif (preg_match('/android/i', $ua)) $os = 'Android';
        elseif (preg_match('/iphone|ipad|ipod/i', $ua)) $os = 'iOS';
        elseif (preg_match('/macintosh|mac os x/i', $ua)) $os = 'macOS';
        elseif (preg_match('/linux/i', $ua)) $os = 'Linux';

        $browser = 'Navegador';
        if (preg_match('/edg/i', $ua)) $browser = 'Microsoft Edge';
        elseif (preg_match('/chrome/i', $ua)) $browser = 'Google Chrome';
        elseif (preg_match('/firefox/i', $ua)) $browser = 'Mozilla Firefox';
        elseif (preg_match('/safari/i', $ua)) $browser = 'Apple Safari';

        $deviceType = preg_match('/mobile|android|iphone|ipad/i', $ua) ? 'Móvil' : 'Escritorio';
        $dispositivo = "{$browser} en {$os} ({$deviceType})";

        $prevData = [
            'meta_dispositivo' => $dispositivo,
            'meta_ip' => $ip,
        ];

        if ($tipoEvent === 'UPDATED') {
            $prevData['meta_valores_anteriores'] = $model->getOriginal();
            $prevData['meta_valores_nuevos'] = $model->getAttributes();
        } elseif ($tipoEvent === 'DELETED') {
            $prevData['meta_valores_anteriores'] = $model->getOriginal();
        } else {
            $prevData['meta_valores_nuevos'] = $model->toArray();
        }

        HistorialProceso::create([
            'usuario_id' => $userId,
            'organizacion_id' => $orgId,
            'tabla_afectada' => $table,
            'registro_id' => $model->id,
            'accion' => $accion,
            'descripcion' => $descripcion,
            'detalles_previos' => $prevData
        ]);
    }
}
