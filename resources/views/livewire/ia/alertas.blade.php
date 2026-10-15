<div class="space-y-8 p-4 md:p-1 transition-colors duration-500">
    <!-- Header: Alertas e Inteligencia Artificial -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/10 pb-6">
        <div>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white italic tracking-tighter uppercase leading-none">
                🤖 Alertas e Inteligencia Artificial
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 font-bold italic">
                Diagnósticos agronómicos predictivos, análisis satelital NDVI y recomendaciones automáticas por sensores.
            </p>
        </div>
        <button wire:click="simularAnalisis" wire:loading.attr="disabled"
                class="px-6 py-3.5 bg-agri-green text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-agri-green/20 hover:scale-105 transition-all flex items-center gap-2 italic">
            <span wire:loading.remove wire:target="simularAnalisis"><i class="fa-solid fa-arrows-rotate"></i> Escaneo de Sensores IA</span>
            <span wire:loading wire:target="simularAnalisis"><i class="fa-solid fa-spinner animate-spin"></i> Analizando parcelas...</span>
        </button>
    </div>

    <!-- SECCIÓN DE ALERTAS Y RECOMENDACIONES CLIMÁTICAS DE IA -->
    <div class="space-y-6">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center font-black text-xl"><i class="fa-solid fa-brain"></i></div>
            <div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white italic uppercase tracking-tight leading-none">Recomendaciones e Indicadores de IA</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Análisis continuo de condiciones climáticas y fisiología vegetal</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($alertasIa as $alerta)
                <div class="bg-white dark:bg-agri-d_bg p-6 rounded-3xl border border-slate-100 dark:border-white/5 shadow-lg relative overflow-hidden group hover:border-agri-green/30 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-3">
                            <span class="px-3 py-1 text-[8px] font-black uppercase tracking-widest rounded-lg
                                @if(($alerta->tipo ?? 'informativo') == 'critico') bg-rose-500/10 text-rose-600 border border-rose-500/20
                                @elseif(($alerta->tipo ?? 'informativo') == 'advertencia') bg-amber-500/10 text-amber-600 border border-amber-500/20
                                @else bg-blue-500/10 text-blue-600 border border-blue-500/20 @endif">
                                {{ strtoupper($alerta->tipo ?? 'IA Sugerencia') }}
                            </span>
                            @if(isset($alerta->confianza))
                                <span class="text-[9px] font-black text-slate-400 font-mono bg-slate-100 dark:bg-white/5 px-2 py-0.5 rounded-md">
                                    Precisión: {{ $alerta->confianza * 100 }}%
                                </span>
                            @endif
                        </div>

                        <h4 class="text-base font-black text-slate-800 dark:text-white mb-2 uppercase leading-tight italic">
                            {{ $alerta->titulo }}
                        </h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 italic leading-relaxed mb-4">
                            {{ $alerta->descripcion ?? $alerta->mensaje ?? '' }}
                        </p>
                    </div>

                    @if(isset($alerta->lote))
                        <div class="pt-3 border-t border-slate-100 dark:border-white/5 text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider italic">
                            🌾 Parcela: {{ $alerta->lote }}
                        </div>
                    @endif
                </div>
            @endforeach

            @foreach($alertasSistema as $alerta)
                <div class="bg-white dark:bg-agri-d_bg p-6 rounded-3xl border border-slate-100 dark:border-white/5 shadow-lg flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-3">
                            <span class="px-3 py-1 text-[8px] font-black uppercase tracking-widest rounded-lg bg-slate-100 dark:bg-white/10 text-slate-500">Sistema</span>
                            <span class="text-[9px] font-bold text-slate-400">{{ $alerta->created_at->diffForHumans() }}</span>
                        </div>
                        <h4 class="text-base font-black text-slate-800 dark:text-white mb-2 uppercase italic leading-tight">{{ $alerta->titulo }}</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 italic leading-relaxed">{{ $alerta->mensaje }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
