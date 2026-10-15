<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DualDatabaseReplicationObserver
{
    public function created(Model $model)
    {
        $this->replicateToPgsql($model, 'created');
    }

    public function updated(Model $model)
    {
        $this->replicateToPgsql($model, 'updated');
    }

    public function deleted(Model $model)
    {
        $this->replicateToPgsql($model, 'deleted');
    }

    public function forceDeleted(Model $model)
    {
        $this->replicateToPgsql($model, 'deleted');
    }

    protected function replicateToPgsql(Model $model, string $action)
    {
        // Evitar replicación en entorno de testing o si el driver primario ya es pgsql
        if (app()->environment('testing') || DB::getDriverName() === 'pgsql') {
            return;
        }

        // Verificar rápido disponibilidad de la conexión pgsql para no ralentizar el flujo si el servidor secundario no responde
        try {
            DB::connection('pgsql')->getPdo();
        } catch (\Throwable $e) {
            return;
        }

        try {
            $table = $model->getTable();
            $keyName = $model->getKeyName() ?: 'id';
            $keyValue = $model->getKey();

            if (!$keyValue) {
                return;
            }

            if ($action === 'created' || $action === 'updated') {
                $attributes = $model->getAttributes();

                // Convertir booleanos de enteros a booleanos nativos para PostgreSQL
                foreach ($attributes as $key => $value) {
                    if (is_bool($value)) {
                        $attributes[$key] = $value ? true : false;
                    }
                }

                // Deshabilitar temporalmente restricciones de llaves foráneas en pgsql durante la inserción
                DB::connection('pgsql')->unprepared("SET session_replication_role = 'replica';");

                DB::connection('pgsql')->table($table)->updateOrInsert(
                    [$keyName => $keyValue],
                    $attributes
                );

                DB::connection('pgsql')->unprepared("SET session_replication_role = 'origin';");
            } elseif ($action === 'deleted') {
                DB::connection('pgsql')->table($table)->where($keyName, $keyValue)->delete();
            }
        } catch (\Throwable $e) {
            Log::warning("Replicación Dual PGSQL [{$model->getTable()}]: " . $e->getMessage());
        }
    }
}
