<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Terreno;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Log;

class SyncClimateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agrosys:sync-climate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza y registra automáticamente los datos del clima para todos los terrenos activos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando sincronización automática de clima - Hora Perú: ' . now()->toDateTimeString());
        Log::info('Ejecutando Sincronización de Clima: ' . now()->toDateTimeString());

        $terrenos = Terreno::where('estado', 1)->get();
        $weatherService = new WeatherService();
        $count = 0;

        foreach ($terrenos as $terreno) {
            if ($terreno->latitud && $terreno->longitud) {
                try {
                    $weatherService->updateCurrentWeather($terreno);
                    $count++;
                    $this->line("Sincronizado: {$terreno->nombre}");
                } catch (\Exception $e) {
                    $this->error("Error en terreno {$terreno->id}: " . $e->getMessage());
                    Log::error("Auto-Sync Climate Error: " . $e->getMessage());
                }
            }
        }

        $this->info("Sincronización finalizada. Se actualizaron {$count} terrenos.");
    }
}
