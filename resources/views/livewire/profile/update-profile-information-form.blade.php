<section class="space-y-6">
    <header class="border-b border-slate-100 dark:border-white/5 pb-4">
        <h2 class="text-2xl font-black text-slate-800 dark:text-white italic tracking-tight">
            {{ __('Información del Perfil Técnico') }}
        </h2>
        <p class="mt-2 text-sm text-slate-500 italic font-medium">
            {{ __("Gestione su identidad digital y sus credenciales agrícolas para la red AgroSys.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="space-y-6" enctype="multipart/form-data">
        <!-- Gestión de Imágenes (Perfil y Portada) -->
        <div class="bg-slate-50 dark:bg-white/5 p-4 md:p-6 rounded-2xl border border-slate-100 dark:border-white/5">
            <h3 class="text-[10px] font-black text-agri-green uppercase tracking-[0.3em] mb-4 italic">Material Visual</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Foto de Perfil -->
                <div class="space-y-2">
                    <x-input-label for="foto_perfil" :value="__('Foto de Perfil')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest px-1" />
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-slate-200 dark:bg-white/10 shrink-0">
                            @if ($foto_perfil)
                                <img src="{{ $foto_perfil->temporaryUrl() }}" class="w-full h-full object-cover">
                            @else
                                <img src="{{ auth()->user()->foto_perfil_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->nombres).'&background=00ba2e&color=fff' }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <input type="file" wire:model="foto_perfil" id="foto_perfil" class="text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-agri-green file:text-white hover:file:bg-emerald-600 transition-all" />
                    </div>
                    <x-input-error :messages="$errors->get('foto_perfil')" />
                </div>

                <!-- Foto de Portada -->
                <div class="space-y-2">
                    <x-input-label for="foto_portada" :value="__('Imagen de Portada')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest px-1" />
                    <div class="flex flex-col space-y-3">
                        <div class="h-16 w-full rounded-xl overflow-hidden bg-slate-200 dark:bg-white/10 border border-dashed border-slate-300 dark:border-white/10">
                            @if ($foto_portada)
                                <img src="{{ $foto_portada->temporaryUrl() }}" class="w-full h-full object-cover">
                            @else
                                <img src="{{ auth()->user()->foto_portada_url ?? 'https://images.unsplash.com/photo-1523348837708-15d4a09cfac2?q=80&w=2670&auto=format&fit=crop' }}" class="w-full h-full object-cover opacity-50">
                            @endif
                        </div>
                        <input type="file" wire:model="foto_portada" id="foto_portada" class="text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-slate-200 dark:file:bg-white/10 file:text-slate-700 dark:file:text-white hover:file:bg-slate-300 transition-all" />
                    </div>
                    <x-input-error :messages="$errors->get('foto_portada')" />
                </div>
            </div>
        </div>

        <!-- Bloque de Identidad -->
        <div class="bg-slate-50 dark:bg-white/5 p-4 md:p-6 rounded-2xl border border-slate-100 dark:border-white/5">
            <h3 class="text-[10px] font-black text-agri-green uppercase tracking-[0.3em] mb-4 italic">Identidad Personal</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <x-input-label for="nombres" :value="__('Nombres')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest px-1" />
                    <x-text-input wire:model="nombres" id="nombres" type="text" class="block w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 rounded-xl text-sm" required autofocus />
                    <x-input-error :messages="$errors->get('nombres')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="apellidos" :value="__('Apellidos')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest px-1" />
                    <x-text-input wire:model="apellidos" id="apellidos" type="text" class="block w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 rounded-xl text-sm" required />
                    <x-input-error :messages="$errors->get('apellidos')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="dni" :value="__('Documento Nacional (DNI)')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest px-1" />
                    <x-text-input wire:model="dni" id="dni" type="text" class="block w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 rounded-xl text-sm tabular-nums" required maxlength="8" />
                    <x-input-error :messages="$errors->get('dni')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="telefono" :value="__('Teléfono / WhatsApp')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest px-1" />
                    <x-text-input wire:model="telefono" id="telefono" type="text" class="block w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 rounded-xl text-sm" />
                    <x-input-error :messages="$errors->get('telefono')" />
                </div>
            </div>
        </div>

        <!-- Bloque Profesional -->
        <div class="bg-slate-50 dark:bg-white/5 p-4 md:p-6 rounded-2xl border border-slate-100 dark:border-white/5">
            <h3 class="text-[10px] font-black text-agri-green uppercase tracking-[0.3em] mb-4 italic">Perfil Profesional y Ubicación</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <x-input-label for="experiencia_anios" :value="__('Años de Experiencia')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest px-1" />
                    <x-text-input wire:model="experiencia_anios" id="experiencia_anios" type="number" class="block w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 rounded-xl text-sm" />
                    <x-input-error :messages="$errors->get('experiencia_anios')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="nivel_educativo" :value="__('Nivel Educativo')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest px-1" />
                    <select wire:model="nivel_educativo" id="nivel_educativo" class="block w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 rounded-xl text-sm focus:ring-agri-green focus:border-agri-green">
                        <option value="">Seleccione nivel</option>
                        <option value="Primaria">Primaria</option>
                        <option value="Secundaria">Secundaria</option>
                        <option value="Técnico">Técnico</option>
                        <option value="Universitario">Universitario</option>
                        <option value="Postgrado">Postgrado</option>
                    </select>
                    <x-input-error :messages="$errors->get('nivel_educativo')" />
                </div>

                <div class="md:col-span-2 space-y-2">
                    <x-input-label for="ubicacion" :value="__('Ubicación Geográfica')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest px-1" />
                    <x-text-input wire:model="ubicacion" id="ubicacion" type="text" class="block w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 rounded-xl text-sm" placeholder="Ej: Ayacucho, Huamanga..." />
                    <x-input-error :messages="$errors->get('ubicacion')" />
                </div>

                <div class="md:col-span-2 space-y-2">
                    <x-input-label for="descripcion" :value="__('Sobre usted (Breve descripción)')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest px-1" />
                    <textarea wire:model="descripcion" id="descripcion" rows="4" class="block w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 rounded-xl text-sm focus:ring-agri-green focus:border-agri-green placeholder:italic" placeholder="Cuéntenos su experiencia en el campo..."></textarea>
                    <x-input-error :messages="$errors->get('descripcion')" />
                </div>
            </div>
        </div>

        <!-- Contacto y Seguridad -->
        <div class="bg-slate-50 dark:bg-white/5 p-4 md:p-6 rounded-2xl border border-slate-100 dark:border-white/5">
            <h3 class="text-[10px] font-black text-agri-green uppercase tracking-[0.3em] mb-4 italic">Seguridad de la Cuenta</h3>

            <div class="space-y-4">
                <div class="space-y-2">
                    <x-input-label for="email" :value="__('Correo Electrónico (No editable)')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest px-1" />
                    <x-text-input wire:model="email" id="email" type="email" class="block w-full bg-slate-100 dark:bg-slate-800 border-slate-200 dark:border-white/10 rounded-xl text-sm cursor-not-allowed opacity-70" disabled />
                    <x-input-error :messages="$errors->get('email')" />
                    <p class="text-[9px] text-slate-400 font-bold uppercase italic mt-1 px-1">Por seguridad, el correo institucional solo puede ser cambiado por soporte técnico.</p>

                    @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                        <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-900/20 rounded-xl">
                            <p class="text-xs font-bold text-amber-700 dark:text-amber-400 flex items-center italic">
                                <i class="fa-solid fa-circle-exclamation mr-2"></i>
                                {{ __('Su dirección de correo electrónico no está verificada.') }}
                            </p>

                            <button wire:click.prevent="sendVerification" class="mt-2 text-xs font-black uppercase text-agri-green hover:underline">
                                {{ __('Re-enviar verificación') }}
                            </button>

                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 text-[10px] font-black text-emerald-600 uppercase tracking-widest">
                                    {{ __('Nuevo enlace enviado.') }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 pt-6">
            <x-action-message class="text-emerald-500 font-black italic text-sm" on="profile-updated">
                <i class="fa-solid fa-check-circle mr-1"></i> {{ __('Cambios guardados con éxito.') }}
            </x-action-message>

            <button type="submit" class="px-12 py-4 bg-agri-green text-white rounded-xl font-black uppercase text-[10px] tracking-[0.2em] shadow-xl shadow-agri-green/20 hover:scale-105 active:scale-95 transition-all italic">
                Actualizar Expediente
            </button>
        </div>
    </form>
</section>
