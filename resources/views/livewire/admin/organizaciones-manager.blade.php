<div class="p-4 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-800 dark:text-white italic tracking-tight">Gestión de Organizaciones</h2>
            <p class="text-[10px] text-agri-green font-black uppercase tracking-widest mt-1">Control Global de Empresas y Cooperativas</p>
        </div>

        <div class="relative w-full md:w-96">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <i class="fa-solid fa-magnifying-glass text-sm"></i>
            </span>
            <input wire:model.live="search" type="text" placeholder="Buscar por nombre o RUC..."
                   class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl text-sm focus:ring-2 focus:ring-agri-green/20 focus:border-agri-green outline-none transition-all">
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-100 dark:border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-white/5 border-b border-slate-100 dark:border-white/5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <th class="px-6 py-5">Organización</th>
                        <th class="px-6 py-5">Identificación (RUC)</th>
                        <th class="px-6 py-5">Propietario / Admin</th>
                        <th class="px-6 py-5 text-center">Miembros</th>
                        <th class="px-6 py-5 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-white/5 text-sm">
                    @foreach($organizaciones as $org)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
                        <td class="px-6 py-5">
                            <div>
                                <p class="font-bold text-slate-800 dark:text-white italic leading-tight">{{ $org->nombre }}</p>
                                <p class="text-[10px] text-slate-400 font-medium truncate max-w-xs">{{ $org->email ?? 'Sin email' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="font-mono text-xs font-bold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-white/5 px-2 py-1 rounded-lg border border-slate-200 dark:border-white/10">
                                {{ $org->ruc }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                $propietario = $org->miembros->where('es_propietario', 1)->first();
                            @endphp
                            @if($propietario)
                                <div class="flex items-center space-x-2">
                                    <div class="w-7 h-7 rounded-lg overflow-hidden border border-slate-200 dark:border-white/10">
                                        <img src="{{ $propietario->usuario->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode($propietario->usuario->nombres).'&color=FFFFFF&background=00ba2e' }}" class="w-full h-full object-cover">
                                    </div>
                                    <span class="font-bold text-slate-700 dark:text-slate-300 text-xs">{{ $propietario->usuario->nombres }}</span>
                                </div>
                            @else
                                <span class="text-rose-400 italic text-[10px]">Sin asignar</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="px-3 py-1 bg-agri-green/10 text-agri-green rounded-full font-black text-[10px]">
                                {{ $org->miembros->count() }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <button wire:click="toggleStatus({{ $org->id }})"
                                    class="flex items-center justify-center space-x-2 px-4 py-1.5 rounded-lg mx-auto transition-all {{ $org->estado ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-rose-50 text-rose-600 hover:bg-rose-100' }}">
                                <span class="text-[10px] font-black uppercase tracking-tighter">{{ $org->estado ? 'Activa' : 'Inactiva' }}</span>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-50 dark:divide-white/5">
            {{ $organizaciones->links() }}
        </div>
    </div>
</div>
