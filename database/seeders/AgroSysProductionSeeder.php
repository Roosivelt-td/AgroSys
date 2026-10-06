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

        // Ejecutar las sentencias SQL deshabilitando temporalmente restricciones de llaves foráneas
        DB::unprepared($sqlContent);

        $this->command->info("¡Dataset de producción cargado exitosamente! 20 Usuarios, 3 Organizaciones, Terrenos, Cultivos, Labores, Cosechas, Ventas y Chats importados.");
    }
}
