<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateDual extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:dual {--seed : Ejecutar los seeders en ambas bases de datos} {--fresh : Borrar y re-migrar ambas bases de datos desde cero}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta las migraciones y seeders simultáneamente en MySQL y PostgreSQL.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fresh = $this->option('fresh');
        $seed = $this->option('seed');

        $action = $fresh ? 'migrate:fresh' : 'migrate';

        $this->info("🚀 [1/2] Ejecutando {$action} en la base de datos MySQL...");
        $mysqlParams = ['--database' => 'mysql'];
        if ($seed) $mysqlParams['--seed'] = true;
        Artisan::call($action, $mysqlParams, $this->output);

        $this->newLine();

        $this->info("🚀 [2/2] Ejecutando {$action} en la base de datos PostgreSQL...");
        $pgsqlParams = ['--database' => 'pgsql'];
        if ($seed) $pgsqlParams['--seed'] = true;
        Artisan::call($action, $pgsqlParams, $this->output);

        $this->newLine();
        $this->info("✅ ¡Migraciones completadas exitosamente en MySQL y PostgreSQL!");
    }
}
