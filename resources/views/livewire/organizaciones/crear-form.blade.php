<div class="p-8">
    <!-- VISTA (FRONTEND) - Solo HTML y Tailwind -->

    <!-- Encabezado del Modal -->
    <div class="mb-8 flex justify-between items-center border-b border-slate-50 dark:border-white/5 pb-6">
        <div>
            <h3 class="text-2xl font-black text-slate-800 dark:text-white italic tracking-tight">Nueva Organización</h3>
            <p class="text-[10px] text-agri-green font-black uppercase tracking-widest mt-1">
                {{ auth()->user()->rol_id === 1 ? 'Módulo de Creación Directa' : 'Módulo de Solicitud de Registro' }}
            </p>
        </div>
        <button @click="$dispatch('close')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-white/5 text-slate-400 hover:text-rose-500 transition-all">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nombre -->
            <div class="md:col-span-2">
                <x-input-label for="nombre" :value="__('Nombre de la Empresa / Fundo')" />
                <x-text-input wire:model="nombre" id="nombre" class="block mt-1 w-full" type="text" required placeholder="Ej. Fundo Agrícola San Juan" />
                <x-input-error :messages="$errors->get('nombre')" class="mt-1" />
            </div>

            <!-- RUC -->
            <div>
                <x-input-label for="ruc" :value="__('Número de RUC')" />
                <x-text-input wire:model="ruc" id="ruc" class="block mt-1 w-full" type="text" required placeholder="10XXXXXXXXX" />
                <x-input-error :messages="$errors->get('ruc')" class="mt-1" />
            </div>

            <!-- Teléfono -->
            <div>
                <x-input-label for="telefono" :value="__('Teléfono de Contacto')" />
                <x-text-input wire:model="telefono" id="telefono" class="block mt-1 w-full" type="text" placeholder="+51 900 000 000" />
                <x-input-error :messages="$errors->get('telefono')" class="mt-1" />
            </div>

            <!-- Email -->
            <div class="md:col-span-2">
                <x-input-label for="email" :value="__('Email Institucional')" />
                <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" placeholder="contacto@empresa.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <!-- Dirección -->
            <div class="md:col-span-2">
                <x-input-label for="direccion" :value="__('Dirección Física')" />
                <x-text-input wire:model="direccion" id="direccion" class="block mt-1 w-full" type="text" placeholder="Calle, Distrito, Provincia..." />
                <x-input-error :messages="$errors->get('direccion')" class="mt-1" />
            </div>

            <!-- Descripción -->
            <div class="md:col-span-2">
                <x-input-label for="descripcion" :value="__('Descripción o Notas')" />
                <textarea wire:model="descripcion" id="descripcion" class="block mt-1 w-full border-slate-200 dark:border-white/10 dark:bg-slate-800 rounded-xl shadow-sm focus:border-agri-green focus:ring-agri-green text-sm" rows="3"></textarea>
                <x-input-error :messages="$errors->get('descripcion')" class="mt-1" />
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex items-center justify-end mt-10 pt-6 border-t border-slate-50 dark:border-white/5">
            <x-secondary-button @click="$dispatch('close')" class="mr-4 px-6 py-3 rounded-xl">
                {{ __('Cancelar') }}
            </x-secondary-button>

            <x-primary-button class="px-10 py-3 bg-agri-green hover:bg-agri-forest shadow-xl shadow-agri-green/20 rounded-xl font-black">
                {{ auth()->user()->rol_id === 1 ? __('Crear Ahora') : __('Enviar Solicitud') }}
            </x-primary-button>
        </div>
    </form>
</div>
