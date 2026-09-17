<?php

namespace App\Services;

use App\Services\AI\AgroTools;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AgroBotService
{
    protected $baseUrl;
    protected $model;

    public function __construct()
    {
        $this->baseUrl = env('OLLAMA_URL', 'http://host.docker.internal:11434') . '/api/generate';
        // Priorizamos el modelo qwen3:1.7b por ↑ 3 de 3
        //$this->model = env('OLLAMA_MODEL', 'deepseek-r1:8b'); // 5.2GB
        //$this->model = env('OLLAMA_MODEL', 'mistral:latest'); // 4.4GB
        //$this->model = env('OLLAMA_MODEL', 'qwen3:4b'); // 2.5GB
        //$this->model = env('OLLAMA_MODEL', 'phi:latest'); // 1.6GB
        $this->model = env('OLLAMA_MODEL', 'qwen3:1.7b'); // 1.4GB
        //$this->model = env('OLLAMA_MODEL', 'tinyllama:latest'); // 637MB

    }

    public function getResponse($mensajeUsuario)
    {
        $user = Auth::user();
        $herramientas = $this->identificarHerramientas($mensajeUsuario);

        $datosFinca = "";
        foreach ($herramientas as $h) {
            $datosFinca .= $this->ejecutarHerramienta($h, $user);
        }

        try {
            // Bajamos el timeout a 60s ya que qwen3:1.7b es muy ligero
            $response = Http::timeout(60)->post($this->baseUrl, [
                'model' => $this->model,
                'prompt' => $this->getAgentPrompt($user, $datosFinca, $mensajeUsuario),
                'stream' => false,
                'options' => [
                    'temperature' => 0.3,
                    'num_predict' => 500, // Respuestas técnicas breves y rápidas
                    'num_ctx' => 2048,    // Contexto optimizado
                ]
            ]);

            if ($response->successful()) {
                $text = $response->json()['response'] ?? "Entendido.";
                return preg_replace('/<think>.*?<\/think>/s', '', $text);
            }

            Log::error("Ollama Error (Status {$response->status()}): " . $response->body());
            return "El motor de IA está cargando. Intenta de nuevo en unos segundos.";

        } catch (\Exception $e) {
            Log::error('Ollama Direct Error: ' . $e->getMessage());
            return "Error de conexión. Asegúrate de tener Ollama corriendo (`ollama serve`).";
        }
    }

    protected function identificarHerramientas($pregunta)
    {
        $pregunta = mb_strtolower($pregunta);
        $seleccionadas = [];

        if (Str::contains($pregunta, ['terreno', 'tierra', 'parcela'])) $seleccionadas[] = 'getTerrenos';
        if (Str::contains($pregunta, ['cultivo', 'sembrado', 'cosecha'])) $seleccionadas[] = 'getCultivos';
        if (Str::contains($pregunta, ['labor', 'trabajo', 'hice'])) $seleccionadas[] = 'getLaboresRecientes';
        if (Str::contains($pregunta, ['insumo', 'gaste', 'abono'])) $seleccionadas[] = 'getInsumosUsados';
        if (Str::contains($pregunta, ['venta', 'dinero', 'gane'])) $seleccionadas[] = 'getResumenFinanciero';

        return array_unique($seleccionadas);
    }

    protected function ejecutarHerramienta($nombre, $user)
    {
        $res = AgroTools::$nombre($user);
        return "\n[DATOS REALES MYSQL]:\n" . json_encode($res, JSON_PRETTY_PRINT) . "\n";
    }

    protected function getAgentPrompt($user, $datos, $pregunta)
    {
        return "Instrucciones: Eres AgroBot, el Asistente Técnico de AgroSys. Ayudas al agricultor {$user->nombres}.
Responde en ESPAÑOL de forma técnica y profesional.

DATOS ACTUALES DE LA FINCA:
{$datos}

REGLAS:
1. Usa los datos reales arriba si están disponibles.
2. Sé muy breve y directo.
3. Si no sabes algo, di que no tienes el registro.

Pregunta del usuario: {$pregunta}
Respuesta de AgroBot:";
    }

    /**
     * Análisis agronómico estricto basado en hechos climáticos reales.
     */
    public function analyzeWeather($weatherData, $cropData = null)
    {
        $prompt = "Actúa como un INGENIERO AGRÓNOMO experto. Analiza estos DATOS REALES capturados por satélite:
        - Temperatura: {$weatherData['temp']}°C
        - Humedad: {$weatherData['humedad']}%
        - Viento: {$weatherData['viento']} km/h
        - Condición: {$weatherData['condicion']}
        ";

        if ($cropData) {
            $prompt .= "\nImpacto directo en el cultivo: {$cropData['nombre']} ({$cropData['variedad']})";
        }

        $prompt .= "\nTAREA: Proporciona 2 recomendaciones TÉCNICAS de manejo de campo inmediatas.
        REGLAS:
        1. NO hables del pronóstico futuro.
        2. Céntrate en lo que el agricultor debe hacer AHORA con este clima.
        3. Formato JSON: [{\"msg\": \"mensaje técnico\", \"priority\": \"Alta/Media/Baja\", \"color\": \"blue/rose/amber/emerald\", \"type\": \"Categoría\"}]";

        try {
            $response = Http::timeout(30)->post($this->baseUrl, [
                'model' => $this->model,
                'prompt' => $prompt,
                'stream' => false,
                'format' => 'json'
            ]);

            if ($response->successful()) {
                $text = $response->json()['response'] ?? '[]';
                return json_decode($text, true) ?: [];
            }
        } catch (\Exception $e) {
            Log::error('Weather AI Analysis Error: ' . $e->getMessage());
        }

        return [];
    }
}
