<x-app-layout>
    <div class="max-w-[1200px] mx-auto animate-in fade-in duration-700">
        <!-- Perfil Estilo Red Social / Profesional -->
        <div class="bg-white dark:bg-slate-900 rounded-b-[3rem] shadow-xl overflow-hidden border border-slate-100 dark:border-white/5">

            <!-- Foto de Portada con Avatar Integrado -->
            <div class="relative h-64 md:h-96 w-full group">
                <img src="{{ Auth::user()->foto_portada_url ?? 'https://images.unsplash.com/photo-1523348837708-15d4a09cfac2?q=80&w=2670&auto=format&fit=crop' }}"
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                     alt="Portada AgroSys">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>

                <!-- Avatar Circular (Dentro de la Portada) -->
                <div class="absolute bottom-6 left-8 md:left-16 flex items-end space-x-6">
                    <div class="w-32 h-32 md:w-44 md:h-44 rounded-full border-4 border-white/20 backdrop-blur-sm shadow-2xl relative group/avatar overflow-hidden">
                        <img src="{{ Auth::user()->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->nombres).'&background=00ba2e&color=fff&size=512' }}"
                             class="w-full h-full object-cover transition-transform duration-500 group-hover/avatar:scale-110">
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover/avatar:opacity-100 transition-all cursor-pointer">
                            <i class="fa-solid fa-camera text-white text-2xl"></i>
                        </div>
                    </div>

                    <!-- Datos Básicos (Sobre la Portada para mayor contraste) -->
                    <div class="pb-2 hidden md:block">
                        <h1 class="text-4xl font-black text-white italic tracking-tighter drop-shadow-lg">
                            {{ Auth::user()->nombres }} {{ Auth::user()->apellidos }}
                        </h1>
                        <p class="text-agri-green font-black uppercase text-xs tracking-[0.3em] drop-shadow-md">
                            {{ Auth::user()->display_role }}
                        </p>
                    </div>
                </div>

                <!-- Menú de Acciones Flotante (Dentro de la Portada) -->
                <div class="absolute bottom-6 right-8 md:right-16" x-data="{ menuOpen: false }">
                    <button @click="menuOpen = !menuOpen"
                            class="w-10 h-10 flex items-center justify-center bg-black/20 backdrop-blur-xl border border-white/20 rounded-xl text-white hover:bg-agri-green transition-all shadow-2xl group">
                        <i class="fa-solid fa-ellipsis text-lg"></i>
                    </button>

                    <!-- Lista Desplegable Premium -->
                    <div x-show="menuOpen"
                         @click.away="menuOpen = false"
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="absolute right-0 bottom-full mb-2 w-56 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-100 dark:border-white/10 z-[60] overflow-hidden">

                        <div class="p-1.5 space-y-0.5">
                            <button @click="menuOpen = false; $dispatch('open-modal', 'modal-edit-info')"
                                    class="w-full flex items-center px-3 py-2 text-[10px] font-black uppercase text-slate-600 dark:text-slate-300 hover:bg-agri-green/10 hover:text-agri-green rounded-lg transition-all tracking-widest italic text-left">
                                <i class="fa-solid fa-user-pen mr-2 text-base opacity-40"></i>
                                Editar Expediente
                            </button>

                            <button @click="menuOpen = false; $dispatch('open-modal', 'modal-change-password')"
                                    class="w-full flex items-center px-3 py-2 text-[10px] font-black uppercase text-slate-600 dark:text-slate-300 hover:bg-agri-green/10 hover:text-agri-green rounded-lg transition-all tracking-widest italic text-left">
                                <i class="fa-solid fa-shield-halved mr-2 text-base opacity-40"></i>
                                Cambiar Contraseña
                            </button>

                            <div class="h-px w-full bg-slate-50 dark:bg-white/5 my-1"></div>

                            <button @click="menuOpen = false; $dispatch('open-modal', 'modal-delete-account')"
                                    class="w-full flex items-center px-3 py-2 text-[10px] font-black uppercase text-rose-500 hover:bg-rose-500/10 rounded-lg transition-all tracking-widest italic text-left">
                                <i class="fa-solid fa-user-xmark mr-2 text-base opacity-40"></i>
                                Eliminar Cuenta
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Botón Editar Portada -->
                <button class="absolute top-4 right-4 p-3 bg-black/20 backdrop-blur-xl border border-white/20 rounded-xl text-white opacity-0 group-hover:opacity-100 transition-all hover:bg-agri-green">
                    <i class="fa-solid fa-camera"></i>
                </button>
            </div>

            <!-- Información Detallada (Debajo de la Portada) -->
            <div class="px-8 md:px-16 py-10 relative">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-12">
                    <div class="space-y-10 flex-1">
                        <!-- Stats Rápidos y Bio -->
                        <div class="flex flex-col md:flex-row md:items-center gap-8">
                            <div class="flex items-center space-x-8 bg-slate-50 dark:bg-white/5 p-4 rounded-2xl border border-slate-100 dark:border-white/5 shrink-0 shadow-sm">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Publicaciones</span>
                                    <span class="text-xl font-black text-slate-800 dark:text-white mt-1">0</span>
                                </div>
                                <div class="w-px h-8 bg-slate-200 dark:bg-white/10"></div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Años Exp.</span>
                                    <span class="text-xl font-black text-slate-800 dark:text-white mt-1">{{ Auth::user()->experiencia_anios ?? 0 }}</span>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-agri-green uppercase tracking-[0.3em] italic">Biografía Profesional</p>
                                <p class="text-sm text-slate-600 dark:text-slate-300 max-w-2xl leading-relaxed italic">
                                    {{ Auth::user()->descripcion ?? 'Sin descripción profesional. Actualice su expediente para compartir su historia agrícola.' }}
                                </p>
                            </div>
                        </div>

                        <!-- Detalles del Expediente Técnico (Visibilidad Total) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 pt-8 border-t border-slate-100 dark:border-white/5">
                            <div class="flex items-center space-x-4 group">
                                <div class="w-12 h-12 rounded-xl bg-slate-50 dark:bg-white/5 flex items-center justify-center text-slate-400 group-hover:text-agri-green transition-colors shadow-inner">
                                    <i class="fa-solid fa-location-dot text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Ubicación Geográfica</p>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200 italic">{{ Auth::user()->ubicacion ?? 'No especificada' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4 group">
                                <div class="w-12 h-12 rounded-xl bg-slate-50 dark:bg-white/5 flex items-center justify-center text-slate-400 group-hover:text-agri-green transition-colors shadow-inner">
                                    <i class="fa-solid fa-graduation-cap text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Nivel Educativo</p>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200 italic">{{ Auth::user()->nivel_educativo ?? 'No registrado' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4 group">
                                <div class="w-12 h-12 rounded-xl bg-slate-50 dark:bg-white/5 flex items-center justify-center text-slate-400 group-hover:text-agri-green transition-colors shadow-inner">
                                    <i class="fa-brands fa-whatsapp text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Contacto Directo</p>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200 tabular-nums">{{ Auth::user()->telefono ?? 'Sin número' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4 group">
                                <div class="w-12 h-12 rounded-xl bg-slate-50 dark:bg-white/5 flex items-center justify-center text-slate-400 group-hover:text-agri-green transition-colors shadow-inner">
                                    <i class="fa-solid fa-envelope text-lg"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Correo Electrónico</p>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200 truncate">{{ Auth::user()->email }}</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4 group">
                                <div class="w-12 h-12 rounded-xl bg-slate-50 dark:bg-white/5 flex items-center justify-center text-slate-400 group-hover:text-agri-green transition-colors shadow-inner">
                                    <i class="fa-solid fa-address-card text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Documento (DNI)</p>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200 tabular-nums">{{ Auth::user()->dni }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Tabs de Navegación del Perfil -->
            <div class="border-t border-slate-100 dark:border-white/5 flex justify-center space-x-12" x-data="{ tab: 'publicaciones' }">
                <button @click="tab = 'publicaciones'"
                        :class="tab === 'publicaciones' ? 'border-t-2 border-slate-800 dark:border-white text-slate-800 dark:text-white' : 'text-slate-400'"
                        class="py-4 flex items-center space-x-2 text-[10px] font-black uppercase tracking-[0.2em] transition-all">
                    <i class="fa-solid fa-table-cells"></i>
                    <span>Publicaciones</span>
                </button>
                <button @click="tab = 'guardado'"
                        :class="tab === 'guardado' ? 'border-t-2 border-slate-800 dark:border-white text-slate-800 dark:text-white' : 'text-slate-400'"
                        class="py-4 flex items-center space-x-2 text-[10px] font-black uppercase tracking-[0.2em] transition-all opacity-50">
                    <i class="fa-regular fa-bookmark"></i>
                    <span>Guardado</span>
                </button>
                <button @click="tab = 'etiquetado'"
                        :class="tab === 'etiquetado' ? 'border-t-2 border-slate-800 dark:border-white text-slate-800 dark:text-white' : 'text-slate-400'"
                        class="py-4 flex items-center space-x-2 text-[10px] font-black uppercase tracking-[0.2em] transition-all opacity-50">
                    <i class="fa-solid fa-user-tag"></i>
                    <span>Etiquetado</span>
                </button>
            </div>
        </div>

        <!-- Área de Contenido de los Tabs (Empty State) -->
        <div class="py-20 flex flex-col items-center justify-center text-center space-y-6">
            <div class="w-24 h-24 rounded-full border-2 border-slate-200 dark:border-white/10 flex items-center justify-center text-slate-300 dark:text-white/20">
                <i class="fa-solid fa-camera-rotate text-4xl"></i>
            </div>
            <div class="space-y-2">
                <h3 class="text-3xl font-black text-slate-800 dark:text-white italic tracking-tighter">No hay publicaciones aún</h3>
                <p class="text-sm text-slate-400 font-medium italic">Cuando compartas fotos de tus cultivos o terrenos, aparecerán aquí.</p>
            </div>
        </div>
    </div>

    <!-- MODAL 1: EDICIÓN DE EXPEDIENTE -->
    <x-modal name="modal-edit-info" :show="false" focusable>
        <div class="bg-white dark:bg-agri-d_bg rounded-2xl overflow-hidden shadow-2xl">
            <div class="bg-[#003a38] px-8 py-4 flex justify-between items-center text-white border-b border-white/5">
                <h3 class="text-xl font-black italic tracking-tighter">Editar Perfil</h3>
                <button @click="$dispatch('close')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10 transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="p-6 md:p-8 max-h-[80vh] overflow-y-auto custom-scrollbar">
                <livewire:profile.update-profile-information />
            </div>
        </div>
    </x-modal>

    <!-- MODAL 2: CAMBIO DE SEGURIDAD (CONTRASEÑA) -->
    <x-modal name="modal-change-password" :show="false" focusable>
        <div class="bg-white dark:bg-agri-d_bg rounded-2xl overflow-hidden shadow-2xl">
            <div class="bg-[#003a38] px-10 py-6 flex justify-between items-center text-white border-b border-white/5">
                <h3 class="text-xl font-black italic tracking-tighter">Seguridad de Acceso</h3>
                <button @click="$dispatch('close')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="p-10 max-h-[80vh] overflow-y-auto custom-scrollbar">
                <livewire:profile.update-password />
            </div>
        </div>
    </x-modal>

    <!-- MODAL 3: ELIMINACIÓN DE CUENTA -->
    <x-modal name="modal-delete-account" :show="false" focusable>
        <div class="bg-white dark:bg-agri-d_bg rounded-2xl overflow-hidden shadow-2xl">
            <div class="bg-rose-600 px-10 py-6 flex justify-between items-center text-white border-b border-white/5">
                <h3 class="text-xl font-black italic tracking-tighter text-white">Baja de Usuario</h3>
                <button @click="$dispatch('close')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="p-10">
                <livewire:profile.delete-user />
            </div>
        </div>
    </x-modal>
</x-app-layout>
