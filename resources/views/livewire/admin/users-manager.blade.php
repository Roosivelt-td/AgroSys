<div class="p-6 space-y-6">
    <!-- Header -->
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

    <!-- Status Alert Messages -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if(session('error'))
        <div class="p-4 bg-rose-100 text-rose-700 rounded-xl text-xs font-bold border border-rose-200">
            {{ session('error') }}
        </div>
    @endif

    <!-- Tabla de Usuarios -->
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
                        <!-- Identidad -->
                        <td class="px-6 py-5">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg overflow-hidden border-2 border-slate-100 dark:border-white/10 shrink-0 shadow-sm">
                                    <img src="{{ $user->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->nombres).'&color=FFFFFF&background=00ba2e' }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 dark:text-white leading-tight">{{ $user->nombres }} {{ $user->apellidos }}</p>
                                    <p class="text-[11px] text-slate-400 font-medium">{{ $user->email }} • DNI: {{ $user->dni ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Rol Global -->
                        <td class="px-6 py-5">
                            <span class="px-2.5 py-1 rounded-lg font-black text-[9px] uppercase tracking-tighter
                                {{ $user->rol_id === 1 ? 'bg-rose-50 text-rose-500 border border-rose-100' : 'bg-agri-mint/30 text-agri-green border border-agri-green/10' }}">
                                {{ $user->rol->nombre ?? 'Usuario' }}
                            </span>
                        </td>

                        <!-- Organizaciones -->
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

                        <!-- Estado -->
                        <td class="px-6 py-5">
                            <button wire:click="toggleStatus({{ $user->id }})"
                                    class="flex items-center space-x-2 px-3 py-1 rounded-full transition-all {{ $user->is_activo ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-rose-50 text-rose-600 hover:bg-rose-100' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ $user->is_activo ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                                <span class="text-[10px] font-black uppercase">{{ $user->is_activo ? 'Activo' : 'Bloqueado' }}</span>
                            </button>
                        </td>

                        <!-- Acciones (Ver Perfil, Editar, Bloquear) -->
                        <td class="px-6 py-5 text-center">
                            <div class="flex justify-center space-x-1">
                                <!-- Ver Perfil y Horas de Actividad -->
                                <button wire:click="openProfileModal({{ $user->id }})"
                                        class="p-2 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                        title="Ver Perfil y Horas de Actividad">
                                    <i class="fa-solid fa-eye text-base"></i>
                                </button>

                                <!-- Editar Usuario -->
                                <button wire:click="openEditModal({{ $user->id }})"
                                        class="p-2 text-slate-400 hover:text-agri-green transition-colors"
                                        title="Editar Usuario">
                                    <i class="fa-regular fa-pen-to-square text-base"></i>
                                </button>

                                <!-- Bloquear / Desbloquear -->
                                <button wire:click="toggleStatus({{ $user->id }})"
                                        class="p-2 text-slate-400 hover:text-rose-500 transition-colors"
                                        title="{{ $user->is_activo ? 'Bloquear Usuario' : 'Desbloquear Usuario' }}">
                                    <i class="fa-solid {{ $user->is_activo ? 'fa-lock-open text-emerald-500' : 'fa-lock text-rose-500' }} text-base"></i>
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

    <!-- MODAL 1: Perfil de Usuario y Gráfico de Horas de Actividad -->
    @if($viewingUser)
    <div wire:key="user-profile-modal-{{ $viewingUser->id }}">
        <x-modal name="modal-user-profile" :show="true" focusable maxWidth="2xl">
            <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-2xl rounded-2xl border border-slate-100 dark:border-white/5">
                <div class="bg-agri-l_card dark:bg-black px-8 py-6 flex justify-between items-center text-slate-800 dark:text-white border-b border-agri-green/10 dark:border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold">
                            <i class="fa-solid fa-user-chart"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black italic tracking-tight">Perfil de Usuario y Actividad</h3>
                            <p class="text-[10px] text-indigo-600 dark:text-indigo-400 uppercase font-black tracking-widest">Métricas de Uso</p>
                        </div>
                    </div>
                    <button wire:click="closeProfileModal" class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar">
                    <!-- User Header -->
                    <div class="flex flex-col sm:flex-row items-center gap-6 p-6 bg-slate-50 dark:bg-black/30 rounded-2xl border border-slate-100 dark:border-white/5">
                        <img src="{{ $viewingUser->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode($viewingUser->nombres).'&color=FFFFFF&background=00ba2e' }}" class="w-20 h-20 rounded-full object-cover shadow-md border-2 border-agri-green">

                        <div class="text-center sm:text-left space-y-1">
                            <h4 class="text-xl font-black text-slate-800 dark:text-white italic">
                                {{ $viewingUser->nombres }} {{ $viewingUser->apellidos }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $viewingUser->email }} • DNI: {{ $viewingUser->dni ?? 'N/A' }}</p>
                            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 pt-1">
                                <span class="px-3 py-0.5 bg-agri-green/10 text-agri-green rounded-full text-[10px] font-black uppercase">
                                    {{ $viewingUser->rol->nombre ?? 'Usuario' }}
                                </span>
                                <span class="px-3 py-0.5 {{ $viewingUser->is_activo ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} rounded-full text-[10px] font-black uppercase">
                                    {{ $viewingUser->is_activo ? 'Activo' : 'Bloqueado' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Cantidad de Terrenos & Hectáreas Card -->
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="p-5 bg-emerald-500/5 dark:bg-emerald-500/10 rounded-2xl border border-emerald-500/20 flex items-center space-x-4">
                            <div class="w-12 h-12 bg-emerald-500/20 text-emerald-600 rounded-xl flex items-center justify-center text-xl shrink-0">
                                <i class="fa-solid fa-mountain"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Terrenos Registrados</p>
                                <p class="text-2xl font-black text-slate-800 dark:text-white italic">{{ $userTerrenosCount }} <span class="text-xs font-bold text-slate-400">terrenos</span></p>
                            </div>
                        </div>

                        <div class="p-5 bg-blue-500/5 dark:bg-blue-500/10 rounded-2xl border border-blue-500/20 flex items-center space-x-4">
                            <div class="w-12 h-12 bg-blue-500/20 text-blue-600 rounded-xl flex items-center justify-center text-xl shrink-0">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Superficie Total</p>
                                <p class="text-2xl font-black text-slate-800 dark:text-white italic">{{ number_format($userTotalHectareas, 2) }} <span class="text-xs font-bold text-slate-400">Ha</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- GRÁFICO: Horas de Actividad x Días -->
                    <div class="p-6 bg-slate-900 text-white rounded-2xl space-y-4 shadow-xl border border-white/10"
                         x-data="{
                            init() {
                                const ctx = document.getElementById('userActivityChartMaster');
                                if (!ctx) return;
                                new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: @js($userActivityChartData['labels']),
                                        datasets: [{
                                            label: 'Horas Estimadas de Uso Activo',
                                            data: @js($userActivityChartData['values']),
                                            backgroundColor: '#10b981',
                                            borderRadius: 8,
                                            hoverBackgroundColor: '#059669'
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: { display: false }
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                ticks: {
                                                    color: '#94a3b8',
                                                    callback: (v) => v + ' h'
                                                },
                                                grid: { color: 'rgba(255, 255, 255, 0.1)' }
                                            },
                                            x: {
                                                ticks: { color: '#94a3b8' },
                                                grid: { display: false }
                                            }
                                        }
                                    }
                                });
                            }
                         }" x-init="init()"
                         wire:key="user-activity-chart-{{ $viewingUser->id }}"
                    >
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <div class="flex items-center gap-2 text-emerald-400 font-black text-xs uppercase tracking-wider">
                                <i class="fa-solid fa-chart-simple text-sm"></i>
                                <span>Métricas de Uso: Horas de Actividad × Día</span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase italic">Últimos 7 días</span>
                        </div>

                        <div class="h-56 relative" wire:ignore>
                            <canvas id="userActivityChartMaster"></canvas>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-slate-50 dark:bg-black/40 border-t border-slate-100 dark:border-white/5 flex justify-end">
                    <button wire:click="closeProfileModal" class="px-8 py-2.5 bg-[#173B27] hover:bg-[#245337] text-white rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-md">
                        Cerrar Perfil
                    </button>
                </div>
            </div>
        </x-modal>
    </div>
    @endif

    <!-- MODAL 2: Editar Usuario -->
    @if($editingUser)
    <div wire:key="user-edit-modal-{{ $editingUser->id }}">
        <x-modal name="modal-edit-user" :show="true" focusable maxWidth="lg">
            <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-2xl rounded-2xl border border-slate-100 dark:border-white/5">
                <div class="bg-agri-l_card dark:bg-black px-8 py-6 flex justify-between items-center text-slate-800 dark:text-white border-b border-agri-green/10 dark:border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-agri-green/20 flex items-center justify-center text-agri-green font-bold">
                            <i class="fa-solid fa-user-pen"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black italic tracking-tight">Editar Información de Usuario</h3>
                            <p class="text-[10px] text-agri-green uppercase font-black tracking-widest">Actualizar Datos</p>
                        </div>
                    </div>
                    <button @click="$dispatch('close')" class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <form wire:submit.prevent="saveUser" class="p-8 space-y-5">
                    <!-- Nombres y Apellidos -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label :value="__('Nombres')" class="text-[10px] font-black uppercase text-slate-400 mb-1" />
                            <input wire:model="editNombres" type="text" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl text-xs font-bold text-slate-800 dark:text-white focus:ring-agri-green outline-none">
                            <x-input-error :messages="$errors->get('editNombres')" class="mt-1 text-xs" />
                        </div>
                        <div>
                            <x-input-label :value="__('Apellidos')" class="text-[10px] font-black uppercase text-slate-400 mb-1" />
                            <input wire:model="editApellidos" type="text" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl text-xs font-bold text-slate-800 dark:text-white focus:ring-agri-green outline-none">
                            <x-input-error :messages="$errors->get('editApellidos')" class="mt-1 text-xs" />
                        </div>
                    </div>

                    <!-- DNI y Teléfono -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label :value="__('DNI (8 dígitos)')" class="text-[10px] font-black uppercase text-slate-400 mb-1" />
                            <input wire:model="editDni" type="text" maxlength="8" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl text-xs font-bold text-slate-800 dark:text-white focus:ring-agri-green outline-none">
                            <x-input-error :messages="$errors->get('editDni')" class="mt-1 text-xs" />
                        </div>
                        <div>
                            <x-input-label :value="__('Teléfono')" class="text-[10px] font-black uppercase text-slate-400 mb-1" />
                            <input wire:model="editTelefono" type="text" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl text-xs font-bold text-slate-800 dark:text-white focus:ring-agri-green outline-none" placeholder="Opcional">
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <x-input-label :value="__('Correo Electrónico')" class="text-[10px] font-black uppercase text-slate-400 mb-1" />
                        <input wire:model="editEmail" type="email" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl text-xs font-bold text-slate-800 dark:text-white focus:ring-agri-green outline-none">
                        <x-input-error :messages="$errors->get('editEmail')" class="mt-1 text-xs" />
                    </div>

                    <!-- Rol Global -->
                    <div>
                        <x-input-label :value="__('Rol Global del Sistema')" class="text-[10px] font-black uppercase text-slate-400 mb-1" />
                        <select wire:model="editRolId" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl text-xs font-bold text-slate-800 dark:text-white focus:ring-agri-green outline-none">
                            @foreach($roles as $rol)
                                <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Estado Activo/Bloqueado -->
                    <div class="flex items-center space-x-3 pt-2">
                        <input wire:model="editIsActivo" type="checkbox" id="editIsActivo" class="w-4 h-4 text-agri-green rounded border-slate-300 focus:ring-agri-green">
                        <label for="editIsActivo" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Cuenta Activa (desmarcar para bloquear usuario)
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100 dark:border-white/5">
                        <button type="button" @click="$dispatch('close')" class="px-6 py-2.5 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-200 transition-all">Cancelar</button>
                        <button type="submit" class="px-8 py-2.5 bg-agri-green hover:bg-emerald-600 text-white rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-md">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
    @endif
</div>
