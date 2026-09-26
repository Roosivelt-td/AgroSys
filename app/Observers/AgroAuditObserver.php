<?php

namespace App\Observers;

use App\Models\HistorialProceso;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class AgroAuditObserver
{
    public function created(Model $model)
    {
        $this->log($model, 'INSERT', 'Creación de registro en ' . $model->getTable());
    }

    public function updated(Model $model)
    {
        // Evitar bucles infinitos si el modelo que se actualiza es HistorialProceso (aunque no debería estar observado)
        if ($model->getTable() === 'historial_procesos') return;

        $this->log($model, 'UPDATE', 'Actualización de registro en ' . $model->getTable(), $model->getOriginal());
    }

    public function deleted(Model $model)
    {
        $this->log($model, 'DELETE', 'Eliminación de registro en ' . $model->getTable(), $model->getOriginal());
    }

    protected function log(Model $model, string $accion, string $descripcion, $previos = null)
    {
        // En registros iniciales o consola, Auth puede ser null
        $userId = Auth::check() ? Auth::id() : (\App\Models\User::value('id'));
        if (!$userId) return; // Si no hay usuarios en la BD aún, no se puede auditar con FK

        $orgId = null;
        if (isset($model->organizacion_id)) {
            $orgId = $model->organizacion_id;
        } elseif (method_exists($model, 'terreno') && $model->terreno) {
            $orgId = $model->terreno->organizacion_id;
        } elseif (method_exists($model, 'cultivo') && $model->cultivo && $model->cultivo->terreno) {
            $orgId = $model->cultivo->terreno->organizacion_id;
        }

        HistorialProceso::create([
            'usuario_id' => $userId,
            'organizacion_id' => $orgId,
            'tabla_afectada' => $model->getTable(),
            'registro_id' => $model->id,
            'accion' => $accion,
            'descripcion' => $descripcion,
            'detalles_previos' => $previos
        ]);
    }
}
