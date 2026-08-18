<div class="bg-white dark:bg-agri-d_bg rounded-2xl overflow-hidden shadow-2xl transition-colors duration-500">
    <!-- Header Especial Alta Seguridad -->
    <div class="p-8 pb-0 flex justify-between items-start">
        <div>
            <p class="text-[10px] text-rose-500 font-black uppercase tracking-[0.2em] mb-1 animate-pulse">Alta de Privilegios Globales</p>
        </div>
        <button @click="$dispatch('close')" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-300 hover:text-rose-500 transition-colors">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>

    <form wire:submit.prevent="save" class="p-8 pt-4 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
            <!-- Nombres -->
            <div class="space-y-1.5">
                <x-input-label for="nombres" :value="__('Nombres')" class="text-xs font-bold text-slate-700 dark:text-slate-300 ml-1" />
                <x-text-input wire:model="nombres" id="nombres" class="block w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-white/10 rounded-xl text-sm h-12 focus:ring-rose-500/20 focus:border-rose-500" type="text" required />
                <x-input-error :messages="$errors->get('nombres')" />
            </div>

            <!-- Apellidos -->
            <div class="space-y-1.5">
                <x-input-label for="apellidos" :value="__('Apellidos')" class="text-xs font-bold text-slate-700 dark:text-slate-300 ml-1" />
                <x-text-input wire:model="apellidos" id="apellidos" class="block w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-white/10 rounded-xl text-sm h-12 focus:ring-rose-500/20 focus:border-rose-500" type="text" required />
                <x-input-error :messages="$errors->get('apellidos')" />
            </div>

            <!-- Email -->
            <div class="space-y-1.5">
                <x-input-label for="email" :value="__('Correo electrónico')" class="text-xs font-bold text-slate-700 dark:text-slate-300 ml-1" />
                <x-text-input wire:model="email" id="email" class="block w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-white/10 rounded-xl text-sm h-12 focus:ring-rose-500/20 focus:border-rose-500" type="email" required />
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <!-- DNI -->
            <div class="space-y-1.5">
                <x-input-label for="dni" :value="__('DNI')" class="text-xs font-bold text-slate-700 dark:text-slate-300 ml-1" />
                <x-text-input wire:model="dni" id="dni" class="block w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-white/10 rounded-xl text-sm h-12 focus:ring-rose-500/20 focus:border-rose-500 tabular-nums" type="text" required maxlength="8" />
                <x-input-error :messages="$errors->get('dni')" />
            </div>

            <!-- Nueva Password -->
            <div class="space-y-1.5 md:col-span-2">
                <x-input-label for="password" :value="__('Contraseña para el nuevo Admin')" class="text-xs font-bold text-slate-700 dark:text-slate-300 ml-1" />
                <x-text-input wire:model="password" id="password" class="block w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-white/10 rounded-xl text-sm h-12 focus:ring-rose-500/20 focus:border-rose-500" type="password" required />
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <!-- Confirmación Crítica -->
            <div class="md:col-span-2 bg-rose-50/50 dark:bg-rose-500/5 p-6 rounded-[2rem] border border-rose-100 dark:border-rose-500/20 space-y-3">
                <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest italic ml-1">Su Contraseña (Para autorizar)</p>
                <x-text-input wire:model="admin_password" id="admin_password"
                               class="block w-full bg-white dark:bg-slate-900 border-rose-200 dark:border-white/10 rounded-xl text-sm h-12 focus:ring-rose-500 focus:border-rose-500"
                               type="password" required placeholder="Confirme su identidad..." />
                <x-input-error :messages="$errors->get('admin_password')" />
            </div>
        </div>

        <!-- Footer Acciones -->
        <div class="flex items-center justify-center gap-4 mt-10">
            <button type="button" @click="$dispatch('close')"
                    class="px-10 py-3.5 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl font-black uppercase text-[10px] tracking-widest text-slate-500 hover:bg-slate-50 transition-all">
                Cancelar
            </button>

            <button type="submit"
                    class="px-12 py-3.5 bg-rose-600 text-white rounded-xl font-black uppercase text-[10px] tracking-widest shadow-xl shadow-rose-600/30 hover:scale-105 active:scale-95 transition-all italic">
                Crear Super Administrador
            </button>
        </div>
    </form>
</div>
