<div class="space-y-0 p-4 md:p-8">
    <!-- Contenedor Principal Estilo ADMIRO -->
    <div class="bg-white dark:bg-agri-d_bg rounded-2xl shadow-2xl overflow-hidden border border-slate-200 dark:border-white/10 transition-colors duration-500">

        <!-- Header: Estilo ADMIRO -->
        <div class="bg-agri-l_card dark:bg-black px-8 py-6 border-b border-agri-green/10 dark:border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors duration-500">
            <div>
                <h2 class="text-xl font-black text-slate-800 dark:text-white tracking-tight italic">Historial de Transiciones: Mi Actividad</h2>
                <p class="text-[10px] text-agri-green font-black uppercase tracking-widest mt-1">Registro Personal de Acciones en la Finca</p>
            </div>

            <div class="relative w-72">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input wire:model.live="search" type="text" placeholder="Buscar en mi historial..."
                       class="w-full pl-9 pr-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl text-xs focus:ring-agri-green outline-none shadow-sm">
            </div>
        </div>

        <!-- Cuerpo de la Tabla -->
        <div class="bg-agri-l_bg dark:bg-agri-d_bg/30 p-6 md:p-12 transition-colors duration-500">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-4">
                    <thead>
                        <tr class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">
                            <th class="px-6 py-2">Autor del Evento</th>
                            <th class="px-6 py-2">Organización</th>
                            <th class="px-6 py-2 text-center">Acción</th>
                            <th class="px-6 py-2">Fecha / Hora</th>
                            <th class="px-6 py-2 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr class="group transition-all duration-300">
                            <!-- Autor con Icono Circular -->
                            <td class="px-6 py-5 first:rounded-l-xl border-y border-l border-slate-100 dark:border-white/5 bg-white dark:bg-agri-d_bg shadow-sm">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 rounded-full bg-agri-l_bg dark:bg-white/5 flex items-center justify-center border border-slate-100 dark:border-white/10 shrink-0 group-hover:scale-110 transition-transform shadow-inner">
                                        <i class="fa-solid fa-user-check text-agri-green text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-800 dark:text-agri-green leading-tight italic">{{ Auth::user()->nombres }} {{ Auth::user()->apellidos }}</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase italic tracking-tighter">{{ Auth::user()->display_role }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Org -->
                            <td class="px-6 py-5 border-y border-slate-100 dark:border-white/5 bg-white dark:bg-agri-d_bg shadow-sm">
                                @if($log->organizacion)
                                    <span class="font-black text-slate-700 dark:text-slate-300 text-xs italic tracking-tighter">{{ $log->organizacion->nombre }}</span>
                                @else
                                    <span class="text-rose-400 font-black text-[10px] uppercase tracking-widest italic">Global</span>
                                @endif
                            </td>

                            <!-- Acción -->
                            <td class="px-6 py-5 border-y border-slate-100 dark:border-white/5 bg-white dark:bg-agri-d_bg text-center shadow-sm">
                                @php
                                    $tabla = $log->tabla_afectada;
                                    $accionUpper = strtoupper($log->accion);
                                    $descLower = strtolower($log->descripcion);

                                    if ($log->accion === 'REGISTRO' && $tabla === 'usuarios') {
                                        $label = '+ TE UNISTE AL SISTEMA';
                                    } elseif ($tabla === 'labores' || str_contains($descLower, 'labor') || str_contains($accionUpper, 'LABOR')) {
                                        $nombreLabor = 'CAMPO';
                                        if (preg_match('/labor de ([a-záéíóúñ\s]+) (realizada|modificada|eliminada)/i', $log->descripcion, $matches)) {
                                            $nombreLabor = mb_strtolower(trim($matches[1]));
                                        } elseif (preg_match('/labor ([a-záéíóúñ\s]+)/i', $log->accion, $matches)) {
                                            $nombreLabor = mb_strtolower(trim($matches[1]));
                                        }

                                        if (str_contains($nombreLabor, 'prepar')) {
                                            $textoBase = 'PREPARACIÓN DE TERRENO';
                                        } elseif (str_contains($nombreLabor, 'siembra') || str_contains($nombreLabor, 'sembrar')) {
                                            $textoBase = 'SIEMBRA DE CULTIVO';
                                        } elseif (str_contains($nombreLabor, 'riego') || str_contains($nombreLabor, 'regar')) {
                                            $textoBase = 'RIEGO DE CULTIVO';
                                        } elseif (str_contains($nombreLabor, 'fumig')) {
                                            $textoBase = 'FUMIGACIÓN DE CULTIVO';
                                        } elseif (str_contains($nombreLabor, 'abon') || str_contains($nombreLabor, 'fertiliz')) {
                                            $textoBase = 'FERTILIZACIÓN DE CULTIVO';
                                        } elseif (str_contains($nombreLabor, 'cosech')) {
                                            $textoBase = 'COSECHA DE CULTIVO';
                                        } elseif (str_contains($nombreLabor, 'aporque')) {
                                            $textoBase = 'APORQUE DE CULTIVO';
                                        } elseif (str_contains($nombreLabor, 'deshierb')) {
                                            $textoBase = 'DESHIERBE DE CULTIVO';
                                        } else {
                                            $textoBase = 'LABOR DE ' . mb_strtoupper($nombreLabor);
                                        }

                                        if (str_contains($descLower, 'edit') || str_contains($descLower, 'actualiz') || str_contains($descLower, 'modific') || str_contains($accionUpper, 'EDICIÓN') || $accionUpper === 'UPDATE') {
                                            $label = "• SE EDITÓ {$textoBase}";
                                        } elseif (str_contains($descLower, 'elimin') || str_contains($accionUpper, 'ELIMINACIÓN') || $accionUpper === 'DELETE') {
                                            $label = "✕ SE ELIMINÓ {$textoBase}";
                                        } else {
                                            $label = "+ {$textoBase} REALIZADA";
                                        }
                                    } elseif ($tabla === 'cosechas' || str_contains($descLower, 'cosecha')) {
                                        if (str_contains($descLower, 'edit') || str_contains($descLower, 'actualiz') || $accionUpper === 'EDICIÓN COSECHA' || $accionUpper === 'UPDATE') {
                                            $label = '• SE EDITÓ UNA COSECHA';
                                        } elseif (str_contains($descLower, 'elimin') || $accionUpper === 'ELIMINACIÓN COSECHA' || $accionUpper === 'DELETE') {
                                            $label = '✕ SE ELIMINÓ UNA COSECHA';
                                        } else {
                                            $label = '+ SE REGISTRÓ COSECHA DE CULTIVO';
                                        }
                                    } elseif ($tabla === 'ventas' || str_contains($descLower, 'venta')) {
                                        if (str_contains($descLower, 'edit') || str_contains($descLower, 'actualiz') || $accionUpper === 'EDICIÓN VENTA' || $accionUpper === 'UPDATE') {
                                            $label = '• SE EDITÓ UNA VENTA';
                                        } elseif (str_contains($descLower, 'elimin') || $accionUpper === 'ELIMINACIÓN VENTA' || $accionUpper === 'DELETE') {
                                            $label = '✕ SE ELIMINÓ UNA VENTA';
                                        } else {
                                            $label = '+ SE REGISTRÓ UNA VENTA';
                                        }
                                    } elseif ($tabla === 'cultivos' || str_contains($descLower, 'cultivo') || str_contains($accionUpper, 'CULTIVO')) {
                                        if (str_contains($descLower, 'edit') || str_contains($descLower, 'actualiz') || $accionUpper === 'EDICIÓN CULTIVO' || $accionUpper === 'UPDATE') {
                                            $label = '• SE EDITÓ UN CULTIVO';
                                        } elseif (str_contains($descLower, 'elimin') || $accionUpper === 'ELIMINACIÓN CULTIVO' || $accionUpper === 'DELETE') {
                                            $label = '✕ SE ELIMINÓ UN CULTIVO';
                                        } else {
                                            $label = '+ SE REGISTRÓ UN CULTIVO';
                                        }
                                    } elseif ($tabla === 'terrenos' || str_contains($descLower, 'terreno') || str_contains($accionUpper, 'TERRENO')) {
                                        if (str_contains($descLower, 'edit') || str_contains($descLower, 'actualiz') || $accionUpper === 'EDICIÓN TERRENO' || $accionUpper === 'UPDATE') {
                                            $label = '• SE EDITÓ UN TERRENO';
                                        } elseif (str_contains($descLower, 'elimin') || $accionUpper === 'ELIMINACIÓN TERRENO' || $accionUpper === 'DELETE') {
                                            $label = '✕ SE ELIMINÓ UN TERRENO';
                                        } else {
                                            $label = '+ SE REGISTRÓ UN TERRENO';
                                        }
                                    } else {
                                        $label = match($accionUpper) {
                                            'SOLICITUD' => '+ ENVIASTE UNA PETICIÓN',
                                            'APROBACIÓN' => '✓ TRÁMITE ACEPTADO',
                                            'RECHAZO' => '✕ PETICIÓN DENEGADA',
                                            'ACTUALIZACIÓN' => '• ACTUALIZASTE DATOS',
                                            default => $log->accion
                                        };
                                    }

                                    // Determinar Color (Verde: Insert/Registro, Celeste: Update/Edición, Rojo: Delete/Eliminación)
                                    if (str_contains($label, '+') || in_array($accionUpper, ['REGISTRO', 'REGISTRO TERRENO', 'REGISTRO CULTIVO', 'REGISTRO COSECHA', 'REGISTRO VENTA', 'EJECUCIÓN DE LABOR', 'INSERT', 'CREATE', 'NUEVO MIEMBRO'])) {
                                        $badgeColor = 'text-emerald-500 bg-emerald-500/10 border-emerald-500/20';
                                    } elseif (str_contains($label, '•') || in_array($accionUpper, ['EDICIÓN TERRENO', 'EDICIÓN CULTIVO', 'EDICIÓN COSECHA', 'EDICIÓN VENTA', 'EDICIÓN DE LABOR', 'UPDATE', 'EDIT', 'ACTUALIZACIÓN', 'APROBACIÓN'])) {
                                        $badgeColor = 'text-sky-500 bg-sky-500/10 border-sky-500/20';
                                    } else {
                                        $badgeColor = 'text-rose-500 bg-rose-500/10 border-rose-500/20';
                                    }
                                @endphp
                                <span class="inline-block px-3 py-1.5 rounded-full text-[10px] font-black tabular-nums tracking-widest border {{ $badgeColor }}">
                                    {{ $label }}
                                </span>
                            </td>

                            <!-- Fecha / Hora -->
                            <td class="px-6 py-5 border-y border-slate-100 dark:border-white/5 bg-white dark:bg-agri-d_bg shadow-sm">
                                <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400 tabular-nums">
                                    <p class="uppercase">{{ $log->created_at->translatedFormat('M d, Y') }}</p>
                                    <p class="opacity-70">{{ $log->created_at->format('H:i:s') }}</p>
                                </div>
                            </td>

                            <!-- Status Button & Details Button -->
                            <td class="px-6 py-5 last:rounded-r-xl border-y border-r border-slate-100 dark:border-white/5 bg-white dark:bg-agri-d_bg text-right shadow-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="showUserDetails({{ $log->id }})"
                                            class="inline-block px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-white/10 font-bold text-[11px] transition-all hover:scale-105 shadow-sm">
                                        ver detalles
                                    </button>
                                    <button wire:click="showDetails({{ $log->id }})"
                                            class="inline-block px-4 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 border border-emerald-100 dark:border-emerald-900/30 font-black text-[10px] uppercase tracking-widest transition-all hover:scale-105">
                                        COMPLETADO
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-10 px-4">
                {{ $logs->links() }}
            </div>
        </div>
    </div>

    <!-- MODAL: Detalles del Perfil, Registro, Terreno, Cultivo, Labor, Cosecha o Venta -->
    @if($selectedUserLog)
    <div wire:key="modal-personal-user-details-wrapper-{{ $selectedUserLog->id }}">
        <x-modal name="personal-user-details" :show="true" focusable>
            <div class="bg-white dark:bg-agri-d_bg overflow-hidden shadow-2xl rounded-2xl border border-slate-100 dark:border-white/5">
                <div class="bg-agri-l_card dark:bg-black px-8 py-6 flex justify-between items-center text-slate-800 dark:text-white border-b border-agri-green/10 dark:border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-agri-green/20 flex items-center justify-center text-agri-green font-bold">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black italic tracking-tight">Detalles de Mi Actividad</h3>
                            <p class="text-[10px] text-agri-green uppercase font-black tracking-widest">Información Registrada</p>
                        </div>
                    </div>
                    <button wire:click="closeUserDetails" class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar">
                    <!-- TERRENO DETAILS CARD -->
                    @if($selectedUserLog->tabla_afectada === 'terrenos' || $selectedTerreno)
                    <div class="p-6 bg-emerald-500/5 dark:bg-emerald-500/10 rounded-2xl border border-emerald-500/20 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-black text-xs uppercase tracking-wider">
                                <i class="fa-solid fa-mountain text-sm"></i>
                                <span>Detalles del Terreno Involucrado</span>
                            </div>
                        </div>

                        @if($selectedTerreno)
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl space-y-1">
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Nombre del Terreno</p>
                                <p class="text-sm font-black text-slate-800 dark:text-white">{{ $selectedTerreno->nombre }}</p>
                            </div>

                            <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl space-y-1">
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Área / Hectáreas</p>
                                <p class="text-sm font-black text-slate-800 dark:text-white">{{ number_format($selectedTerreno->hectareas, 2) }} Ha</p>
                            </div>

                            <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl space-y-1">
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Tipo de Tenencia</p>
                                <p class="text-sm font-bold text-slate-800 dark:text-white capitalize">{{ $selectedTerreno->tipo_tenencia }}</p>
                            </div>

                            <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl space-y-1">
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Calidad de Suelo</p>
                                <p class="text-sm font-bold text-slate-800 dark:text-white capitalize">{{ $selectedTerreno->calidad_suelo ?? 'Franco' }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- CULTIVO DETAILS CARD -->
                    @if($selectedUserLog->tabla_afectada === 'cultivos' || $selectedCultivo)
                    <div class="p-6 bg-sky-500/5 dark:bg-sky-500/10 rounded-2xl border border-sky-500/20 space-y-4">
                        <div class="flex items-center gap-2 text-sky-600 dark:text-sky-400 font-black text-xs uppercase tracking-wider">
                            <i class="fa-solid fa-seedling text-sm"></i>
                            <span>Detalles del Cultivo Involucrado</span>
                        </div>

                        @if($selectedCultivo)
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl space-y-1">
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Cultivo / Lote</p>
                                <p class="text-sm font-black text-slate-800 dark:text-white">
                                    {{ $selectedCultivo->detalleCatalogo->nombre ?? 'Cultivo' }} (Lote {{ $selectedCultivo->nombre_lote }})
                                </p>
                            </div>

                            <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl space-y-1">
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Área Destinada</p>
                                <p class="text-sm font-black text-slate-800 dark:text-white">{{ number_format($selectedCultivo->area_destinada, 2) }} Ha</p>
                            </div>

                            <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl space-y-1">
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Variedad</p>
                                <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $selectedCultivo->variedad ?: 'Estándar' }}</p>
                            </div>

                            <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl space-y-1">
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Estado de Campaña</p>
                                <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $selectedCultivo->estado }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- LABOR DETAILS CARD -->
                    @if($selectedUserLog->tabla_afectada === 'labores' || $selectedLabor)
                    <div class="p-6 bg-purple-500/5 dark:bg-purple-500/10 rounded-2xl border border-purple-500/20 space-y-4">
                        <div class="flex items-center gap-2 text-purple-600 dark:text-purple-400 font-black text-xs uppercase tracking-wider">
                            <i class="fa-solid fa-list-check text-sm"></i>
                            <span>Detalles de la Labor Ejecutada</span>
                        </div>

                        @if($selectedLabor)
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl space-y-1">
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Tipo de Labor / Actividad</p>
                                <p class="text-sm font-black text-slate-800 dark:text-white">
                                    {{ $selectedLabor->detalleCatalogo->nombre ?? ($selectedLabor->categoria ?? 'Labor Campo') }}
                                </p>
                            </div>

                            <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl space-y-1">
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Costo Total</p>
                                <p class="text-sm font-black text-emerald-600">S/ {{ number_format($selectedLabor->costo_total ?? 0, 2) }}</p>
                            </div>

                            @if($selectedLabor->cultivo)
                            <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl space-y-1">
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Cultivo / Lote</p>
                                <p class="text-sm font-bold text-slate-800 dark:text-white">
                                    {{ $selectedLabor->cultivo->detalleCatalogo->nombre ?? 'Cultivo' }} (Lote {{ $selectedLabor->cultivo->nombre_lote }})
                                </p>
                            </div>
                            @endif

                            @if($selectedLabor->cultivo && $selectedLabor->cultivo->terreno)
                            <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl space-y-1">
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Terreno Asignado</p>
                                <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $selectedLabor->cultivo->terreno->nombre }} ({{ number_format($selectedLabor->cultivo->terreno->hectareas, 2) }} Ha)</p>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- COSECHA DETAILS CARD -->
                    @if($selectedUserLog->tabla_afectada === 'cosechas' || $selectedCosecha)
                    <div class="p-6 bg-amber-500/5 dark:bg-amber-500/10 rounded-2xl border border-amber-500/20 space-y-4">
                        <div class="flex items-center gap-2 text-amber-600 dark:text-amber-400 font-black text-xs uppercase tracking-wider">
                            <i class="fa-solid fa-basket-shopping text-sm"></i>
                            <span>Detalles de Cosecha Obtenida</span>
                        </div>

                        @if($selectedCosecha)
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl space-y-1">
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Cantidad Producida</p>
                                <p class="text-sm font-black text-slate-800 dark:text-white">
                                    {{ number_format($selectedCosecha->cantidad_kg, 2) }} {{ $selectedCosecha->unidad_medida }}
                                </p>
                            </div>

                            <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl space-y-1">
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Calidad del Producto</p>
                                <p class="text-sm font-bold text-slate-800 dark:text-white uppercase">{{ $selectedCosecha->calidad }}</p>
                            </div>

                            @if($selectedCosecha->labor && $selectedCosecha->labor->cultivo)
                            <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl space-y-1 sm:col-span-2">
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Origen del Cultivo</p>
                                <p class="text-sm font-bold text-slate-800 dark:text-white">
                                    {{ $selectedCosecha->labor->cultivo->detalleCatalogo->nombre ?? 'Cultivo' }} (Lote {{ $selectedCosecha->labor->cultivo->nombre_lote }})
                                </p>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Timestamps Card -->
                    <div class="p-6 bg-agri-green/5 dark:bg-agri-green/10 rounded-2xl border border-agri-green/20 space-y-3">
                        <div class="flex items-center gap-2 text-agri-green font-black text-xs uppercase tracking-wider">
                            <i class="fa-regular fa-calendar-check text-sm"></i>
                            <span>Fecha y Hora de Registro</span>
                        </div>

                        <p class="text-sm font-black text-slate-800 dark:text-white">
                            {{ $selectedUserLog->created_at->translatedFormat('d \\d\\e F, Y - H:i:s') }}
                        </p>
                    </div>

                    <!-- DISPOSITIVO Y UBICACIÓN CARD -->
                    @if(isset($selectedUserLog->detalles_previos['dispositivo']) || isset($selectedUserLog->detalles_previos['meta_dispositivo']))
                    <div class="p-6 bg-slate-900 text-white rounded-2xl space-y-3 shadow-inner border border-white/10">
                        <div class="flex items-center gap-2 text-agri-green font-black text-xs uppercase tracking-wider">
                            <i class="fa-solid fa-laptop-code text-sm"></i>
                            <span>Dispositivo y Ubicación de Acceso</span>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-4 text-xs pt-1">
                            <div class="p-3.5 bg-white/5 rounded-xl border border-white/10 space-y-1">
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Dispositivo & Navegador</p>
                                <p class="font-black text-white">
                                    {{ $selectedUserLog->detalles_previos['dispositivo'] ?? $selectedUserLog->detalles_previos['meta_dispositivo'] }}
                                </p>
                            </div>

                            <div class="p-3.5 bg-white/5 rounded-xl border border-white/10 space-y-1">
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Dirección IP & Red</p>
                                <p class="font-black text-emerald-400">
                                    {{ $selectedUserLog->detalles_previos['ip'] ?? ($selectedUserLog->detalles_previos['meta_ip'] ?? '127.0.0.1') }}
                                </p>
                            </div>

                            @if(isset($selectedUserLog->detalles_previos['ubicacion_ip']))
                            <div class="p-3.5 bg-white/5 rounded-xl border border-white/10 space-y-1 sm:col-span-2">
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Ubicación Geográfica Estimada</p>
                                <p class="font-bold text-white flex items-center gap-2">
                                    <i class="fa-solid fa-location-dot text-rose-400"></i>
                                    <span>{{ $selectedUserLog->detalles_previos['ubicacion_ip'] }}</span>
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Action Description & AI Change Analysis -->
                    @php
                        $analysis = $selectedUserLog->explicacion_cambios;
                    @endphp
                    <div class="p-6 bg-slate-50 dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-white/10 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-black text-xs uppercase tracking-wider">
                                <i class="fa-solid fa-wand-magic-sparkles text-sm"></i>
                                <span>Análisis Inteligente de la Acción</span>
                            </div>
                            @if(!empty($analysis['cambios']))
                                <span class="px-2.5 py-0.5 bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-300 rounded-full text-[9px] font-black uppercase">
                                    {{ count($analysis['cambios']) }} {{ count($analysis['cambios']) === 1 ? 'Modificación' : 'Modificaciones' }}
                                </span>
                            @endif
                        </div>

                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 italic leading-relaxed">
                            "{{ $analysis['resumen'] }}"
                        </p>

                        @if(!empty($analysis['cambios']))
                        <div class="pt-3 border-t border-slate-200 dark:border-white/10 space-y-2">
                            <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest italic">
                                Comparativo de Cambios Detectados:
                            </p>
                            <div class="space-y-2">
                                @foreach($analysis['cambios'] as $cambio)
                                <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-white/5 flex flex-col sm:flex-row sm:items-center justify-between text-xs gap-2 shadow-xs">
                                    <span class="font-black text-slate-800 dark:text-slate-200 uppercase text-[10px]">
                                        {{ $cambio['campo'] }}
                                    </span>
                                    <div class="flex items-center gap-2 text-[11px] font-bold">
                                        <span class="px-2 py-0.5 bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 rounded-lg line-through">
                                            {{ $cambio['anterior'] }}
                                        </span>
                                        <i class="fa-solid fa-arrow-right text-[10px] text-slate-400"></i>
                                        <span class="px-2.5 py-0.5 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 rounded-lg">
                                            {{ $cambio['nuevo'] }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="p-6 bg-slate-50 dark:bg-black/40 border-t border-slate-100 dark:border-white/5 flex justify-end">
                    <button wire:click="closeUserDetails" class="px-8 py-2.5 bg-[#173B27] hover:bg-[#245337] text-white rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-md">
                        Cerrar
                    </button>
                </div>
            </div>
        </x-modal>
    </div>
    @endif

    <!-- Modal Audit Original -->
    @if($selectedItem)
    <div wire:key="modal-personal-audit-wrapper-{{ $selectedItem->id }}">
        <x-modal name="personal-audit" :show="true" focusable>
            <div class="bg-white dark:bg-agri-d_bg overflow-hidden shadow-2xl rounded-2xl border border-slate-100 dark:border-white/5">
                <div class="bg-agri-l_card dark:bg-black px-10 py-8 flex justify-between items-center text-slate-800 dark:text-white border-b border-agri-green/10 dark:border-white/10">
                    <div>
                        <h3 class="text-2xl font-black italic tracking-tighter">Mi Registro de Actividad</h3>
                        <p class="text-[10px] opacity-60 uppercase font-black tracking-[0.3em]">ID Transacción #{{ $selectedItem->id }}</p>
                    </div>
                    <button wire:click="closeDetails" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
                        <i class="fa-solid fa-xmark text-2xl"></i>
                    </button>
                </div>

                <div class="p-10 space-y-8 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    <div class="bg-agri-l_bg dark:bg-black/20 p-8 rounded-2xl border border-slate-100 dark:border-white/5 text-center shadow-inner">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 italic">Descripción del Suceso</p>
                        <p class="text-sm font-bold text-slate-700 dark:text-slate-200 italic leading-relaxed">
                            "{{ $selectedItem->descripcion }}"
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="p-6 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-50 dark:border-white/5 flex items-center space-x-4">
                            <div class="w-12 h-12 bg-agri-green/10 rounded-full flex items-center justify-center text-agri-green shadow-inner">
                                <i class="fa-solid fa-table-list"></i>
                            </div>
                            <div>
                                <p class="text-[9px] uppercase font-black text-slate-400 tracking-widest">Módulo Afectado</p>
                                <p class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tighter italic">{{ $selectedItem->tabla_afectada }}</p>
                            </div>
                        </div>
                        <div class="p-6 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-50 dark:border-white/5 flex items-center space-x-4">
                            <div class="w-12 h-12 bg-blue-500/10 rounded-full flex items-center justify-center text-blue-500 shadow-inner">
                                <i class="fa-solid fa-hashtag"></i>
                            </div>
                            <div>
                                <p class="text-[9px] uppercase font-black text-slate-400 tracking-widest">Referencia</p>
                                <p class="text-sm font-black text-slate-800 dark:text-white">#{{ $selectedItem->registro_id }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-10 bg-slate-50 dark:bg-black/40 border-t border-slate-100 dark:border-white/5 flex justify-end">
                    <button wire:click="closeDetails" class="px-12 py-3 bg-agri-green text-white rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all shadow-xl shadow-agri-green/30 hover:scale-105">
                        Cerrar Detalle
                    </button>
                </div>
            </div>
        </x-modal>
    </div>
    @endif
</div>
