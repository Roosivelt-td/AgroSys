<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AgroSysProductionSeeder extends Seeder
{
    /**
     * Ejecuta el script SQL de producción de AgroSys (1 Año de datos simulados).
     */
    public function run(): void
    {
        $sqlPath = database_path('seeders/agrosys_production_demo_dataset.sql');

        if (!File::exists($sqlPath)) {
            $this->command->error("El archivo SQL {$sqlPath} no existe.");
            return;
        }

        $this->command->info("Cargando dataset de producción histórico de AgroSys desde SQL...");

        $sqlContent = File::get($sqlPath);
        $driver = DB::getDriverName();

        // Adaptar sintaxis de MySQL a PostgreSQL / SQLite
        if ($driver !== 'mysql') {
            $sqlContent = str_replace('`', '"', $sqlContent);
            $sqlContent = str_replace('NOW()', 'CURRENT_TIMESTAMP', $sqlContent);

            if ($driver === 'pgsql') {
                $sqlContent = str_replace('SET FOREIGN_KEY_CHECKS = 0;', "SET session_replication_role = 'replica';", $sqlContent);
                $sqlContent = str_replace('SET FOREIGN_KEY_CHECKS = 1;', "SET session_replication_role = 'origin';", $sqlContent);

                // Convertir booleanos numéricos (1/0) a TRUE/FALSE para columnas tipo boolean en PostgreSQL
                $sqlContent = preg_replace('/,\s*1\s*,\s*(\'202\d-)/i', ", TRUE, $1", $sqlContent);
                $sqlContent = preg_replace('/,\s*0\s*,\s*(\'202\d-)/i', ", FALSE, $1", $sqlContent);

                $sqlContent = preg_replace_callback('/INSERT\s+IGNORE\s+INTO\s+("[^"]+")\s*\(([^)]+)\)\s*VALUES\s*(.*?);/is', function($matches) {
                    $table = $matches[1];
                    $columns = $matches[2];
                    $values = trim($matches[3]);
                    $firstCol = trim(explode(',', $columns)[0]);

                    return "INSERT INTO {$table} ({$columns}) VALUES {$values} ON CONFLICT ({$firstCol}) DO NOTHING;";
                }, $sqlContent);
            } elseif ($driver === 'sqlite') {
                $sqlContent = str_replace('SET FOREIGN_KEY_CHECKS = 0;', 'PRAGMA foreign_keys = OFF;', $sqlContent);
                $sqlContent = str_replace('SET FOREIGN_KEY_CHECKS = 1;', 'PRAGMA foreign_keys = ON;', $sqlContent);
                $sqlContent = str_replace('INSERT IGNORE INTO', 'INSERT OR IGNORE INTO', $sqlContent);
            }
        }

        // Ejecutar las sentencias SQL preparadas
        DB::unprepared($sqlContent);

        $this->command->info("¡Dataset de producción cargado exitosamente! 20 Usuarios, 3 Organizaciones, Terrenos, Cultivos, Labores, Cosechas, Ventas y Chats importados.");
    }
}
