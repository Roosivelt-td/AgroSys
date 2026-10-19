<div class="p-8">
    <!-- Encabezado del Modal -->
    <div class="mb-6 flex justify-between items-center border-b border-slate-100 dark:border-white/5 pb-6">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 rounded-2xl bg-agri-green/10 text-agri-green flex items-center justify-center text-2xl font-black">
                <i class="fa-solid {{ $step === 1 ? 'fa-building-wheat' : 'fa-credit-card' }}"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white italic tracking-tight">
                    {{ $step === 1 ? 'Adquirir Nueva Organización' : 'Pasarela de Pago Segura' }}
                </h3>
                <p class="text-[10px] text-agri-green font-black uppercase tracking-widest mt-0.5">
                    {{ $step === 1 ? 'Paso 1 de 2: Datos de Empresa y Suscripción' : 'Paso 2 de 2: Confirmación y Cargo por Tarjeta' }}
                </p>
            </div>
        </div>
        <button @click="$dispatch('close')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-white/5 text-slate-400 hover:text-rose-500 transition-all">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>

    <!-- PASO 1: DATOS DE LA EMPRESA Y PLAN DE SUSCRIPCIÓN -->
    @if($step === 1)
    <form wire:submit.prevent="irAPagar" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nombre de la Empresa -->
            <div class="md:col-span-2">
                <x-input-label for="nombre" :value="__('Nombre de la Empresa / Fundo *')" />
                <x-text-input wire:model="nombre" id="nombre" class="block mt-1 w-full uppercase" type="text" required placeholder="Ej. Fundo Agrícola San Juan" />
                <x-input-error :messages="$errors->get('nombre')" class="mt-1" />
            </div>

            <!-- RUC -->
            <div>
                <x-input-label for="ruc" :value="__('Número de RUC (11 dígitos) *')" />
                <x-text-input wire:model="ruc" id="ruc" class="block mt-1 w-full" type="text" required placeholder="10XXXXXXXXX" maxlength="11" />
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

            <!-- SELECCIÓN DEL PLAN / AÑOS DE SUSCRIPCIÓN -->
            <div class="md:col-span-2 p-5 bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/10 rounded-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-black text-slate-800 dark:text-white uppercase italic">Duración de la Suscripción Anual</h4>
                        <p class="text-[10px] font-bold text-slate-400 mt-0.5">Seleccione el número de años de vigencia para la organización</p>
                    </div>
                    <span class="px-3 py-1 bg-agri-green/10 text-agri-green rounded-lg font-black text-[10px] uppercase">
                        S/ {{ number_format($tarifa_anual, 2) }} / Año
                    </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach([1 => '1 Año', 2 => '2 Años', 3 => '3 Años', 5 => '5 Años'] as $a => $label)
                        <button type="button" wire:click="$set('anios_suscripcion', {{ $a }})"
                                class="p-3 rounded-xl border text-center transition-all cursor-pointer flex flex-col items-center justify-center
                                {{ $anios_suscripcion === $a ? 'bg-agri-green text-white border-agri-green shadow-md scale-105' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-300 hover:border-agri-green' }}">
                            <span class="text-xs font-black uppercase italic">{{ $label }}</span>
                            <span class="text-[9px] opacity-80 mt-0.5 font-bold">S/ {{ number_format($a * $tarifa_anual, 0) }}</span>
                        </button>
                    @endforeach
                </div>

                <!-- Resumen de Adquisición y Vencimiento -->
                <div class="grid grid-cols-2 gap-4 pt-3 border-t border-slate-200/60 dark:border-white/5 text-[10px]">
                    <div>
                        <span class="font-black text-slate-400 uppercase tracking-widest italic block">Fecha de Adquisición:</span>
                        <span class="font-black text-slate-800 dark:text-white uppercase mt-0.5 block">📅 {{ now()->format('d/m/Y') }}</span>
                    </div>
                    <div>
                        <span class="font-black text-slate-400 uppercase tracking-widest italic block">Fecha de Vencimiento:</span>
                        <span class="font-black text-emerald-600 dark:text-emerald-400 uppercase mt-0.5 block">⏳ {{ now()->addYears((int)$anios_suscripcion)->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Descripción -->
            <div class="md:col-span-2">
                <x-input-label for="descripcion" :value="__('Descripción o Notas Adicionales')" />
                <textarea wire:model="descripcion" id="descripcion" class="block mt-1 w-full border-slate-200 dark:border-white/10 dark:bg-slate-800 rounded-xl shadow-sm focus:border-agri-green focus:ring-agri-green text-sm" rows="2"></textarea>
                <x-input-error :messages="$errors->get('descripcion')" class="mt-1" />
            </div>
        </div>

        <!-- Botones de Acción Paso 1 -->
        <div class="flex items-center justify-between mt-8 pt-6 border-t border-slate-100 dark:border-white/5">
            <div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic block">Monto a Pagar:</span>
                <span class="text-2xl font-black text-agri-green italic">S/ {{ number_format($anios_suscripcion * $tarifa_anual, 2) }}</span>
            </div>

            <div class="flex items-center gap-3">
                <x-secondary-button @click="$dispatch('close')" class="px-5 py-3 rounded-xl">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <button type="submit" class="px-8 py-3.5 bg-agri-green hover:bg-emerald-600 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-agri-green/20 hover:scale-105 transition-all flex items-center gap-2 italic">
                    <span>Continuar al Pago</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </form>
    @endif

    <!-- PASO 2: PASARELA DE PAGO POR TARJETA DE CRÉDITO / DÉBITO -->
    @if($step === 2)
    <div class="space-y-6">
        <!-- VISTA PREVIA INTERACTIVA DE TARJETA DIGITAL -->
        <div class="bg-gradient-to-tr from-[#002f2d] via-[#004845] to-[#008f39] p-6 rounded-3xl text-white shadow-2xl space-y-6 relative overflow-hidden border border-white/20">
            <div class="flex justify-between items-center">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] opacity-70">AgroSys Payments</span>
                <div class="flex gap-2 text-2xl">
                    <i class="fa-brands fa-cc-visa"></i>
                    <i class="fa-brands fa-cc-mastercard"></i>
                </div>
            </div>

            <div class="space-y-1">
                <p class="text-[9px] font-black uppercase opacity-60 tracking-widest">Número de Tarjeta</p>
                <p class="text-xl md:text-2xl font-mono font-black tracking-widest">
                    {{ $tarjeta_numero ? wordwrap(str_replace(' ', '', $tarjeta_numero), 4, ' ', true) : '•••• •••• •••• ••••' }}
                </p>
            </div>

            <div class="flex justify-between items-end text-xs font-mono">
                <div>
                    <p class="text-[8px] uppercase opacity-60 font-black">Titular de la Tarjeta</p>
                    <p class="font-black uppercase truncate max-w-[200px]">{{ $tarjeta_titular ?: 'NOMBRE Y APELLIDO' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[8px] uppercase opacity-60 font-black">Vencimiento</p>
                    <p class="font-black">{{ $tarjeta_expiracion ?: 'MM/AA' }}</p>
                </div>
            </div>
        </div>

        <!-- FORMULARIO DE TARJETA -->
        <form wire:submit.prevent="procesarPago" class="space-y-4">
            <!-- Número de Tarjeta -->
            <div>
                <x-input-label for="tarjeta_numero" :value="__('Número de Tarjeta (16 dígitos) *')" />
                <div class="relative mt-1" x-data="{
                    formatCard(e) {
                        let input = e.target;
                        if (e.inputType === 'deleteContentBackward') return;
                        let digits = input.value.replace(/\D/g, '').slice(0, 16);
                        let formatted = digits.match(/.{1,4}/g)?.join(' ') || digits;
                        input.value = formatted;
                        $wire.set('tarjeta_numero', formatted, false);
                    }
                }">
                    <x-text-input wire:model="tarjeta_numero" x-on:input="formatCard($event)" id="tarjeta_numero" class="block w-full pl-10 font-mono tracking-wider" type="text" placeholder="4532 1234 5678 9012" maxlength="19" required />
                    <i class="fa-solid fa-credit-card absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>
                <x-input-error :messages="$errors->get('tarjeta_numero')" class="mt-1" />
            </div>

            <!-- Titular de la Tarjeta -->
            <div>
                <x-input-label for="tarjeta_titular" :value="__('Nombre del Titular de la Tarjeta *')" />
                <x-text-input wire:model="tarjeta_titular" id="tarjeta_titular" class="block mt-1 w-full uppercase" type="text" placeholder="COMO FIGURA EN LA TARJETA" required />
                <x-input-error :messages="$errors->get('tarjeta_titular')" class="mt-1" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Expiración (MM/AA) -->
                <div>
                    <x-input-label for="tarjeta_expiracion" :value="__('Expiración (MM/AA) *')" />
                    <div x-data="{
                        formatExp(e) {
                            let val = e.target.value;
                            if (e.inputType === 'deleteContentBackward') {
                                $wire.set('tarjeta_expiracion', val, false);
                                return;
                            }
                            let num = val.replace(/\D/g, '').slice(0, 4);
                            if (num.length === 0) {
                                e.target.value = '';
                                $wire.set('tarjeta_expiracion', '', false);
                                return;
                            }
                            let first = parseInt(num.charAt(0));
                            if (num.length === 1 && first >= 2 && first <= 9) {
                                e.target.value = '0' + first + '/';
                                $wire.set('tarjeta_expiracion', e.target.value, false);
                                return;
                            }
                            if (num.length >= 2) {
                                let m = parseInt(num.substring(0, 2));
                                if (m > 12) m = 12;
                                if (m === 0) m = 1;
                                let monthStr = m < 10 ? '0' + m : '' + m;
                                let yearStr = num.substring(2, 4);
                                let formatted = yearStr !== '' ? monthStr + '/' + yearStr : monthStr + '/';
                                e.target.value = formatted;
                                $wire.set('tarjeta_expiracion', formatted, false);
                            } else {
                                e.target.value = num;
                                $wire.set('tarjeta_expiracion', num, false);
                            }
                        }
                    }">
                        <x-text-input wire:model="tarjeta_expiracion" x-on:input="formatExp($event)" id="tarjeta_expiracion" class="block mt-1 w-full font-mono text-center" type="text" placeholder="08/28" maxlength="5" required />
                    </div>
                    <x-input-error :messages="$errors->get('tarjeta_expiracion')" class="mt-1" />
                </div>

                <!-- CVV -->
                <div>
                    <x-input-label for="tarjeta_cvv" :value="__('Código CVV (3 dígitos) *')" />
                    <div x-data="{
                        formatCvv(e) {
                            let val = e.target.value.replace(/\D/g, '').slice(0, 3);
                            e.target.value = val;
                            $wire.set('tarjeta_cvv', val, false);
                        }
                    }">
                        <x-text-input wire:model="tarjeta_cvv" x-on:input="formatCvv($event)" id="tarjeta_cvv" class="block mt-1 w-full font-mono text-center" type="password" placeholder="123" maxlength="3" required />
                    </div>
                    <x-input-error :messages="$errors->get('tarjeta_cvv')" class="mt-1" />
                </div>
            </div>

            <!-- RESUMEN DEL COBRO -->
            <div class="p-4 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-500/20 rounded-2xl space-y-2 text-[10px]">
                <div class="flex justify-between font-bold text-slate-600 dark:text-slate-300">
                    <span>Organización a Activar:</span>
                    <span class="font-black text-slate-800 dark:text-white uppercase">{{ $nombre }}</span>
                </div>
                <div class="flex justify-between font-bold text-slate-600 dark:text-slate-300">
                    <span>Duración Contratada:</span>
                    <span class="font-black text-slate-800 dark:text-white">{{ $anios_suscripcion }} Año(s) (hasta {{ now()->addYears((int)$anios_suscripcion)->format('d/m/Y') }})</span>
                </div>
                <div class="flex justify-between font-black text-emerald-700 dark:text-emerald-400 text-xs border-t border-emerald-500/20 pt-2">
                    <span>Total A Debitar:</span>
                    <span>S/ {{ number_format($anios_suscripcion * $tarifa_anual, 2) }}</span>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN PASO 2 -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-white/5">
                <button type="button" wire:click="volverADatos" class="px-5 py-3 bg-slate-200 dark:bg-white/10 text-slate-700 dark:text-slate-300 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-300 transition-all italic flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Volver a Datos</span>
                </button>

                <button type="submit" wire:loading.attr="disabled"
                        class="px-8 py-3.5 bg-agri-green hover:bg-emerald-600 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-agri-green/20 hover:scale-105 transition-all flex items-center gap-2 italic">
                    <span wire:loading.remove wire:target="procesarPago">
                        <i class="fa-solid fa-lock text-xs mr-1"></i> Confirmar y Pagar S/ {{ number_format($anios_suscripcion * $tarifa_anual, 2) }}
                    </span>
                    <span wire:loading wire:target="procesarPago" class="flex items-center gap-2">
                        <i class="fa-solid fa-spinner animate-spin"></i> Procesando Pago Bancario...
                    </span>
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
