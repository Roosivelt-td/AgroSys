<div class="p-6 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-800 dark:text-white italic tracking-tight">Directorio Global de Usuarios</h2>
            <p class="text-[10px] text-agri-green font-black uppercase tracking-widest mt-1">Control de Identidad y Acceso al Sistema</p>
        </div>

        <div class="relative w-full md:w-96">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <i class="fa-solid fa-magnifying-glass text-sm"></i>
            </span>
            <input wire:model.live="search" type="text" placeholder="Buscar por nombre, email, DNI..."
                   class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl text-sm focus:ring-2 focus:ring-agri-green/20 focus:border-agri-green outline-none transition-all">
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-white/5 border-b border-slate-100 dark:border-white/5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <th class="px-6 py-5">Identidad</th>
                        <th class="px-6 py-5">Rol Global</th>
                        <th class="px-6 py-5">Organizaciones</th>
                        <th class="px-6 py-5">Estado</th>
                        <th class="px-6 py-5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-white/5 text-sm">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg overflow-hidden border-2 border-slate-100 dark:border-white/10 shrink-0 shadow-sm">
                                    <img src="{{ $user->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->nombres).'&color=FFFFFF&background=00ba2e' }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 dark:text-white leading-tight">{{ $user->nombres }} {{ $user->apellidos }}</p>
                                    <p class="text-[11px] text-slate-400 font-medium">{{ $user->email }} • DNI: {{ $user->dni }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="px-2.5 py-1 rounded-lg font-black text-[9px] uppercase tracking-tighter
                                {{ $user->rol_id === 1 ? 'bg-rose-50 text-rose-500 border border-rose-100' : 'bg-agri-mint/30 text-agri-green border border-agri-green/10' }}">
                                {{ $user->rol->nombre }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-wrap gap-1">
                                @forelse($user->membresias as $membresia)
                                    <span class="text-[9px] px-2 py-0.5 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-md font-bold">
                                        {{ $membresia->organizacion->nombre }}
                                    </span>
                                @empty
                                    <span class="text-[9px] text-slate-400 italic">Sin organizaciones</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <button wire:click="toggleStatus({{ $user->id }})"
                                    class="flex items-center space-x-2 px-3 py-1 rounded-full transition-all {{ $user->is_activo ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-rose-50 text-rose-600 hover:bg-rose-100' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ $user->is_activo ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                                <span class="text-[10px] font-black uppercase">{{ $user->is_activo ? 'Activo' : 'Inactivo' }}</span>
                            </button>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="flex justify-center space-x-2">
                                <button class="p-2 text-slate-400 hover:text-agri-green transition-colors">
                                    <i class="fa-regular fa-pen-to-square text-lg"></i>
                                </button>
                                <button class="p-2 text-slate-400 hover:text-rose-500 transition-colors">
                                    <i class="fa-regular fa-trash-can text-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-50 dark:border-white/5">
            {{ $users->links() }}
        </div>
    </div>
</div>
