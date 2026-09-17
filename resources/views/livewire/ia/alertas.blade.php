<div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                <span class="mr-2">🧠</span> Alertas IA
            </h2>
            <p class="text-gray-600 dark:text-gray-400">Predicciones y recomendaciones basadas en datos de telemetría y clima.</p>
        </div>
        <button
            wire:click="simularAnalisis"
            wire:loading.attr="disabled"
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center transition-all"
        >
            <span wire:loading.remove wire:target="simularAnalisis">🔄 Ejecutar Escaneo IA</span>
            <span wire:loading wire:target="simularAnalisis">⚙️ Analizando datos...</span>
        </button>
    </div>

    @if($loading)
        <div class="flex flex-col items-center justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mb-4"></div>
            <p class="text-green-600 font-medium">La IA está procesando los sensores y modelos climáticos...</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($alertasIa as $alerta)
                <div class="relative overflow-hidden rounded-xl border-l-4 p-4 shadow-sm transition-transform hover:scale-105
                    @if(($alerta->tipo ?? 'informativo') == 'critico') border-red-500 bg-red-50 dark:bg-red-900/20
                    @elseif(($alerta->tipo ?? 'informativo') == 'advertencia') border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20
                    @else border-blue-500 bg-blue-50 dark:bg-blue-900/20 @endif">

                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider
                            @if(($alerta->tipo ?? 'informativo') == 'critico') text-red-700 dark:text-red-400
                            @elseif(($alerta->tipo ?? 'informativo') == 'advertencia') text-yellow-700 dark:text-yellow-400
                            @else text-blue-700 dark:text-blue-400 @endif">
                            {{ $alerta->tipo ?? 'IA Sugerencia' }}
                        </span>
                        @if(isset($alerta->confianza))
                            <span class="text-xs font-mono text-gray-500">Confianza: {{ $alerta->confianza * 100 }}%</span>
                        @endif
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $alerta->titulo }}</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">{{ $alerta->descripcion ?? $alerta->mensaje ?? '' }}</p>

                    @if(isset($alerta->cultivo))
                        <div class="mb-3 text-xs font-bold text-green-700 dark:text-green-400">
                            Lote: {{ $alerta->cultivo->nombre_lote }}
                        </div>
                    @endif

                    <div class="mt-4 flex gap-2">
                        <button class="text-xs px-3 py-1.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-white rounded hover:bg-gray-300 transition text-nowrap">Ignorar</button>
                        <button class="text-xs px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 transition text-nowrap">Aplicar Sugerencia</button>
                    </div>
                </div>
            @endforeach

            @foreach($alertasSistema as $alerta)
                <div class="relative overflow-hidden rounded-xl border-l-4 p-4 shadow-sm bg-gray-50 dark:bg-gray-700/50 border-gray-400">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">Sistema</span>
                        <span class="text-[10px] text-gray-400">{{ $alerta->created_at->diffForHumans() }}</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $alerta->titulo }}</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $alerta->mensaje }}</p>
                </div>
            @endforeach
        </div>
    @endif
</div>
