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

        // Verificar disponibilidad de la conexión pgsql
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

                // Convertir tipos de datos para compatibilidad nativa con PostgreSQL
                foreach ($attributes as $key => $value) {
                    if (is_bool($value) || in_array($key, ['is_activo', 'es_personalizado', 'es_alquilado', 'es_finalizada', 'es_pagado', 'es_ia', 'eliminado_todos'])) {
                        $attributes[$key] = (bool)$value;
                    } elseif (is_array($value) || is_object($value)) {
                        $attributes[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
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
            Log::error("Error Replicación Dual PGSQL [{$model->getTable()}]: " . $e->getMessage());
        }
    }
}
