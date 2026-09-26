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
        $ollamaUrl = env('OLLAMA_URL', 'http://127.0.0.1:11434');
        $this->baseUrl = rtrim($ollamaUrl, '/') . '/api/generate';
        $this->model = env('OLLAMA_MODEL', 'qwen3:1.7b');
    }

    /**
     * Motor unificado de IA: Intenta Google Gemini API primero y Ollama como fallback
     */
    protected function callAI($prompt, $jsonMode = false, $temperature = 0.3, $maxTokens = 800)
    {
        $driver = config('services.gemini.driver') ?: env('AI_DRIVER', 'gemini');
        $geminiKey = config('services.gemini.key') ?: env('GEMINI_API_KEY');
        $geminiModel = config('services.gemini.model') ?: env('GEMINI_MODEL', 'gemini-3.5-flash');
        $geminiModelsToTry = array_unique([$geminiModel, 'gemini-3.5-flash', 'gemini-3.5-flash-lite', 'gemini-3.6-flash']);

        // 1. Intentar con Google Gemini (Modelos en orden de preferencia)
        if (($driver === 'gemini' || !empty($geminiKey)) && !empty($geminiKey) && strlen($geminiKey) > 10) {
            foreach ($geminiModelsToTry as $currentGeminiModel) {
                try {
                    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$currentGeminiModel}:generateContent?key={$geminiKey}";

                    $generationConfig = [
                        'temperature' => (float)$temperature,
                        'maxOutputTokens' => (int)$maxTokens,
                    ];

                    if ($jsonMode) {
                        $generationConfig['responseMimeType'] = 'application/json';
                    }

                    $response = Http::timeout(25)
                        ->withHeaders(['Content-Type' => 'application/json'])
                        ->post($url, [
                            'contents' => [
                                [
                                    'parts' => [
                                        ['text' => $prompt]
                                    ]
                                ]
                            ],
                            'generationConfig' => $generationConfig
                        ]);

                    if ($response->successful()) {
                        $json = $response->json();
                        $parts = $json['candidates'][0]['content']['parts'] ?? [];
                        $text = '';
                        foreach ($parts as $p) {
                            if (isset($p['text'])) {
                                $text .= $p['text'];
                            }
                        }

                        if (!empty($text)) {
                            $text = preg_replace('/<think>.*?<\/think>/s', '', trim($text));
                            $text = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($text));
                            return trim($text);
                        }
                    }

                    Log::warning("Gemini API Error para modelo {$currentGeminiModel} (Status {$response->status()}): " . $response->body() . ". Probando siguiente modelo...");
                } catch (\Exception $e) {
                    Log::warning("Gemini API Exception para modelo {$currentGeminiModel}: " . $e->getMessage());
                }
            }
        }

        // 2. Fallback a Ollama (IA Local)
        $ollamaModel = $this->model ?: env('OLLAMA_MODEL', 'qwen3:1.7b');
        $urlsToTry = array_unique([
            $this->baseUrl,
            'http://127.0.0.1:11434/api/generate',
            'http://localhost:11434/api/generate'
        ]);

        foreach ($urlsToTry as $targetUrl) {
            try {
                $ollamaPayload = [
                    'model' => $ollamaModel,
                    'prompt' => $prompt,
                    'stream' => false,
                    'options' => [
                        'temperature' => (float)$temperature,
                        'num_predict' => (int)$maxTokens,
                    ]
                ];

                if ($jsonMode) {
                    $ollamaPayload['format'] = 'json';
                }

                $response = Http::timeout(45)->post($targetUrl, $ollamaPayload);

                if ($response->successful()) {
                    $text = $response->json()['response'] ?? null;
                    if (!empty($text)) {
                        return preg_replace('/<think>.*?<\/think>/s', '', trim($text));
                    }
                }

                Log::error("Ollama Error on {$targetUrl} (Status {$response->status()}): " . $response->body());
            } catch (\Exception $e) {
                Log::error("Ollama Direct Error on {$targetUrl}: " . $e->getMessage());
            }
        }

        return null;
    }

    public function getResponse($mensajeUsuario)
    {
        $user = Auth::user();
        $herramientas = $this->identificarHerramientas($mensajeUsuario);

        $datosFinca = "";
        foreach ($herramientas as $h) {
            $datosFinca .= $this->ejecutarHerramienta($h, $user);
        }

        $prompt = $this->getAgentPrompt($user, $datosFinca, $mensajeUsuario);
        $reply = $this->callAI($prompt, false, 0.3, 500);

        if ($reply) {
            return $reply;
        }

        return "El servicio de IA no respondió. Revisa la configuración de GEMINI_API_KEY o la conexión con Ollama.";
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
        $nombre = $user?->nombres ?? 'Agricultor';
        return "Instrucciones: Eres AgroBot, el Asistente Técnico de AgroSys. Ayudas al agricultor {$nombre}.
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
     * Análisis agronómico basado en hechos climáticos reales.
     */
    public function analyzeWeather($weatherData, $cropData = null)
    {
        $w = (array)$weatherData;
        $temp = $w['temperatura'] ?? $w['temp'] ?? 20;
        $hum = $w['humedad'] ?? 60;
        $viento = $w['viento_kmh'] ?? $w['viento'] ?? 10;
        $cond = $w['condicion'] ?? 'Estable';

        $prompt = "Actúa como un INGENIERO AGRÓNOMO experto. Analiza estos DATOS REALES capturados por satélite:
- Temperatura: {$temp}°C
- Humedad: {$hum}%
- Viento: {$viento} km/h
- Condición: {$cond}
";

        if ($cropData) {
            $prompt .= "\nImpacto directo en el cultivo: {$cropData['nombre']} ({$cropData['variedad']})";
        }

        $prompt .= "\nTAREA: Proporciona 2 recomendaciones TÉCNICAS de manejo de campo inmediatas.
REGLAS:
1. NO hables del pronóstico futuro.
2. Céntrate en lo que el agricultor debe hacer AHORA con este clima.
3. Formato JSON estricto: [{\"msg\": \"mensaje técnico\", \"priority\": \"Alta/Media/Baja\", \"color\": \"blue/rose/amber/emerald\", \"type\": \"Categoría\"}]";

        $text = $this->callAI($prompt, false, 0.2, 2048);

        if ($text) {
            // Intentar extraer el array JSON si la IA devolvió texto alrededor
            if (preg_match('/\[.*\]/s', $text, $matches)) {
                $text = $matches[0];
            }
            $data = json_decode($text, true);
            return is_array($data) ? $data : [];
        }

        return [];
    }

    /**
     * Cerebro de Coordinación Estratégica: Cruza JSON + Clima + Historial + Finanzas
     */
    public function coordinateStrategicPlan($context)
    {
        if (isset($context['weather']) && is_object($context['weather'])) {
            $context['weather'] = (array)$context['weather'];
        }

        $w = $context['weather'] ?? [];
        $wTemp = $w['temperatura'] ?? $w['temp'] ?? 20;
        $wHum = $w['humedad'] ?? 60;
        $wViento = $w['viento_kmh'] ?? $w['viento'] ?? 10;
        $wCond = $w['condicion'] ?? 'Estable';
        $wLluvia = $w['prob_lluvia'] ?? 0;
        $wForecast = $w['forecast'] ?? [];

        $diasCultivo = $context['dias_cultivo'] ?? 0;
        $diasTotales = $context['dias_totales'] ?? 120;
        $pctProgreso = $diasTotales > 0 ? round(($diasCultivo / $diasTotales) * 100) : 0;
        $totalInversion = number_format($context['stats']['total'] ?? 0, 2);

        $prompt = "Actúa como un INGENIERO AGRÓNOMO Y CEREBRO DE INTELIGENCIA ESTRATÉGICA DE AGROSYS.
Analiza con máxima precisión técnica el estado del cultivo seleccionado:

1. FICHA TÉCNICA DEL CULTIVO:
- Cultivo: {$context['crop_name']}
- Progreso Biológico: Día {$diasCultivo} de {$diasTotales} (Progreso: {$pctProgreso}%) | Etapa: {$context['etapa_nombre']}
- Inversión Acumulada: S/ {$totalInversion}

2. PERFIL TÉCNICO Y LABORES REQUERIDAS (JSON MODELO):
" . json_encode($context['json_profile'] ?? [], JSON_UNESCAPED_UNICODE) . "

3. CLIMA Y TELEMETRÍA EN TIEMPO REAL:
- Temperatura: {$wTemp}°C | Humedad: {$wHum}% | Viento: {$wViento} km/h
- Condición: {$wCond} | Lluvia: {$wLluvia}%
- Pronóstico 7 días: " . json_encode($wForecast, JSON_UNESCAPED_UNICODE) . "

4. HISTORIAL OPERATIVO (REGISTROS MYSQL):
- Labores Ejecutadas: " . ($context['historial_labores'] ?: 'Sin labores previas registradas.') . "
- Historial Clima Reciente: " . ($context['historial_clima'] ?: 'Sin registros anteriores.') . "

INSTRUCCIONES DE FORMATO OBLIGATORIO:
Organiza la respuesta estrictamente en LISTAS CON VIÑETAS (-) muy cortas, resumidas y directas al grano.

### ✅ LO QUE SE DEBE HACER HOY
- **[Labor/Acción 1]:** [Explicación de máximo 1 o 2 líneas].
- **[Labor/Acción 2]:** [Explicación de máximo 1 o 2 líneas].

### 🚫 LO QUE NO SE DEBE HACER
- **[Prohibición/Riesgo 1]:** [Motivo técnico o restricción climática breve].
- **[Prohibición/Riesgo 2]:** [Motivo técnico o restricción climática breve].

### 📊 PROYECCIÓN Y FINANZAS
- **Estado del Ciclo:** Día {$diasCultivo} de {$diasTotales} ({$pctProgreso}%) - Etapa: {$context['etapa_nombre']}.
- **Eficiencia de Inversión:** Invertido S/ {$totalInversion}. [Sugerencia ejecutiva de 1 línea para optimizar gastos].

REGLAS ESTRICTAS:
- TODO debe presentarse en VIÑETAS (-).
- NO escribas párrafos largos ni bloques de texto denso.
- Máximo 2 a 3 viñetas por sección, sumamente resumidas.
- Sin saludos, introducciones ni explicaciones de relleno.";

        $reply = $this->callAI($prompt, false, 0.2, 2048);

        if ($reply) {
            return $reply;
        }

        return "El servicio de IA (Gemini / Ollama) no respondió. Revisa la configuración de tu GEMINI_API_KEY en el archivo .env.";
    }
}
