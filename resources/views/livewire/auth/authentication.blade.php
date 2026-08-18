<div class="min-h-screen w-full relative overflow-hidden bg-black font-sans flex items-center justify-center">
    <!-- Background Image with Cinematic Overlay -->
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=2832&auto=format&fit=crop"
             alt="Campos Agrícolas"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-br from-black/95 via-black/40 to-black/90"></div>
    </div>

    <!-- Main Responsive Grid Container -->
    <div class="relative z-10 w-full max-w-[1600px] mx-auto h-screen flex flex-col lg:flex-row items-center justify-center px-6 md:px-20 gap-10">

        <!-- Left Section: Branding (Hidden on Mobile) -->
        <div class="hidden lg:flex flex-1 flex-col items-start select-none animate-in fade-in slide-in-from-left-10 duration-1000">
            <h1 class="text-[8rem] xl:text-[11rem] font-black italic tracking-tighter drop-shadow-[0_20px_20px_rgba(0,0,0,0.8)] leading-none whitespace-nowrap">
                <span class="bg-gradient-to-b from-[#55cd44] via-[#2d9e1c] to-[#1b5e0f] bg-clip-text text-transparent py-2">Agro</span><span class="bg-gradient-to-b from-[#ff8a00] via-[#e65100] to-[#b71c1c] bg-clip-text text-transparent py-2">Sys</span>
            </h1>
            <p class="text-white text-2xl xl:text-3xl font-bold mt-6 tracking-tight drop-shadow-xl italic opacity-80 max-w-xl">
                Tecnología que hace crecer el campo
            </p>
        </div>

        <!-- Right Section: Auth Card & Icons -->
        <div class="flex flex-col items-center justify-center w-full max-w-[450px] animate-in fade-in zoom-in duration-1000">

            <!-- Transparent Auth Card -->
            <div class="w-full bg-black/40 backdrop-blur-3xl border border-white/10 rounded-[4rem] p-10 md:p-14 shadow-[0_50px_100px_-20px_rgba(0,0,0,1)] transition-all duration-700 hover:border-white/20 relative overflow-hidden">

                <!-- Inner Branding -->
                <div class="text-center mb-10">
                    <h2 class="text-4xl font-black italic tracking-tighter drop-shadow-lg mb-2 whitespace-nowrap">
                        <span class="bg-gradient-to-b from-[#55cd44] to-[#1b5e0f] bg-clip-text text-transparent py-1">Agro</span><span class="bg-gradient-to-b from-[#ff8a00] to-[#b71c1c] bg-clip-text text-transparent py-1">Sys</span>
                    </h2>
                    <p class="text-white/30 text-[10px] font-black uppercase tracking-[0.4em] italic">
                        @if($mode === 'login') Iniciar Sesión @elseif($mode === 'register') Registro @else Recuperar Cuenta @endif
                    </p>
                    <div class="w-14 h-1 bg-gradient-to-r from-agri-green to-orange-600 rounded-full mx-auto mt-4 opacity-40"></div>
                </div>

                <x-auth-session-status class="mb-6" :status="session('status')" />

                <!-- LOGIN FORM -->
                @if($mode === 'login')
                <form wire:submit="login" class="space-y-6">
                    <div class="relative">
                        <div class="flex items-center bg-[#eef2f7] rounded-xl overflow-hidden shadow-inner h-14 border-b-4 border-agri-green transition-all">
                            <div class="w-12 flex items-center justify-center text-agri-green border-r border-slate-200/50">
                                <i class="fa-regular fa-id-badge text-lg"></i>
                            </div>
                            <input wire:model="loginForm.email" type="email" required autofocus placeholder="raul@agrosys.com" class="flex-1 bg-transparent border-none text-slate-800 text-sm focus:ring-0 outline-none px-4 font-bold placeholder:text-slate-400">
                        </div>
                        <x-input-error :messages="$errors->get('loginForm.email')" class="mt-1" />
                    </div>

                    <div class="relative" x-data="{ show: false }">
                        <div class="flex items-center bg-[#eef2f7] rounded-xl overflow-hidden shadow-inner h-14 border-b-4 border-slate-300 focus-within:border-agri-green transition-all">
                            <div class="w-12 flex items-center justify-center text-slate-400 border-r border-slate-200/50">
                                <i class="fa-solid fa-key text-lg"></i>
                            </div>
                            <input wire:model="loginForm.password" :type="show ? 'text' : 'password'" required placeholder="••••••••" class="flex-1 bg-transparent border-none text-slate-800 text-sm focus:ring-0 outline-none px-4 font-bold placeholder:text-slate-400">
                            <button type="button" @click="show = !show" class="w-10 h-full flex items-center justify-center text-slate-300 hover:text-agri-green transition-colors">
                                <i class="fa-regular" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('loginForm.password')" class="mt-1" />
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-agri-green text-white rounded-full font-black uppercase text-[14px] tracking-[0.3em] shadow-[0_20px_40px_-10px_rgba(0,186,46,0.5)] hover:scale-105 active:scale-95 transition-all mt-6 relative overflow-hidden group/btn italic">
                        <span class="relative z-10">Iniciar sesión</span>
                        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover/btn:translate-y-0 transition-transform duration-500"></div>
                    </button>

                    <div class="text-center pt-2">
                        <button type="button" wire:click="setMode('forgot')" class="text-[12px] font-black text-white/20 hover:text-agri-green uppercase tracking-[0.3em] transition-colors italic">
                            ¿Olvidaste tu contraseña?
                        </button>
                    </div>
                </form>
                @endif

                <!-- REGISTER FORM -->
                @if($mode === 'register')
                <form wire:submit="register" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="relative">
                            <div class="flex items-center bg-[#eef2f7] rounded-xl overflow-hidden shadow-inner h-12 border-b-2 border-agri-green">
                                <input wire:model="nombres" type="text" required placeholder="Nombres" class="w-full bg-transparent border-none text-slate-800 text-xs focus:ring-0 px-4 font-bold">
                            </div>
                            <x-input-error :messages="$errors->get('nombres')" class="mt-1" />
                        </div>
                        <div class="relative">
                            <div class="flex items-center bg-[#eef2f7] rounded-xl overflow-hidden shadow-inner h-12 border-b-2 border-agri-green">
                                <input wire:model="apellidos" type="text" required placeholder="Apellidos" class="w-full bg-transparent border-none text-slate-800 text-xs focus:ring-0 px-4 font-bold">
                            </div>
                            <x-input-error :messages="$errors->get('apellidos')" class="mt-1" />
                        </div>
                    </div>

                    <div class="relative">
                        <div class="flex items-center bg-[#eef2f7] rounded-xl overflow-hidden shadow-inner h-12 border-b-2 border-agri-green">
                            <div class="w-10 flex items-center justify-center text-agri-green border-r border-slate-200/30">
                                <i class="fa-regular fa-address-card"></i>
                            </div>
                            <input wire:model="dni"
                                   type="text"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   maxlength="8"
                                   required
                                   onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                   placeholder="DNI (8 dígitos)"
                                   class="flex-1 bg-transparent border-none text-slate-800 text-xs focus:ring-0 px-4 font-bold placeholder:text-slate-400">
                        </div>
                        <x-input-error :messages="$errors->get('dni')" class="mt-1" />
                    </div>

                    <div class="relative">
                        <div class="flex items-center bg-[#eef2f7] rounded-xl overflow-hidden shadow-inner h-12 border-b-2 border-agri-green">
                            <div class="w-10 flex items-center justify-center text-agri-green border-r border-slate-200/30">
                                <i class="fa-regular fa-envelope"></i>
                            </div>
                            <input wire:model="email" type="email" required placeholder="Email" class="flex-1 bg-transparent border-none text-slate-800 text-xs focus:ring-0 px-4 font-bold">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div class="relative">
                        <div class="flex items-center bg-[#eef2f7] rounded-xl overflow-hidden shadow-inner h-12 border-b-2 border-slate-300 focus-within:border-agri-green">
                            <div class="w-10 flex items-center justify-center text-slate-400 border-r border-slate-200/30">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <input wire:model="password" type="password" required placeholder="Contraseña" class="flex-1 bg-transparent border-none text-slate-800 text-xs focus:ring-0 px-4 font-bold">
                        </div>
                    </div>

                    <div class="relative">
                        <div class="flex items-center bg-[#eef2f7] rounded-xl overflow-hidden shadow-inner h-12 border-b-2 border-slate-300 focus-within:border-agri-green">
                            <div class="w-10 flex items-center justify-center text-slate-400 border-r border-slate-200/30">
                                <i class="fa-solid fa-lock-open"></i>
                            </div>
                            <input wire:model="password_confirmation" type="password" required placeholder="Confirmar" class="flex-1 bg-transparent border-none text-slate-800 text-xs focus:ring-0 px-4 font-bold">
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />

                    <button type="submit" class="w-full py-3.5 bg-agri-green text-white rounded-full font-black uppercase text-[12px] tracking-[0.2em] shadow-lg hover:scale-105 active:scale-95 transition-all mt-4 relative overflow-hidden group/btn italic">
                        <span class="relative z-10">Unirme ahora</span>
                        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover/btn:translate-y-0 transition-transform duration-500"></div>
                    </button>
                </form>
                @endif

                <!-- FORGOT PASSWORD FORM -->
                @if($mode === 'forgot')
                <form wire:submit="sendResetLink" class="space-y-6">
                    <p class="text-white/50 text-[14px] text-center italic leading-relaxed">
                        Ingresa tu email institucional y te enviaremos las instrucciones de recuperación.
                    </p>
                    <div class="relative">
                        <div class="flex items-center bg-[#eef2f7] rounded-xl overflow-hidden shadow-inner h-14 border-b-4 border-agri-green transition-all">
                            <div class="w-12 flex items-center justify-center text-agri-green border-r border-slate-200/50">
                                <i class="fa-solid fa-paper-plane"></i>
                            </div>
                            <input wire:model="forgotEmail" type="email" required placeholder="tu-correo@agrosys.com" class="flex-1 bg-transparent border-none text-slate-800 text-sm focus:ring-0 outline-none px-4 font-bold placeholder:text-slate-400">
                        </div>
                        <x-input-error :messages="$errors->get('forgotEmail')" class="mt-1" />
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-agri-green text-white rounded-full font-black uppercase text-[12px] tracking-[0.3em] shadow-lg hover:scale-105 active:scale-95 transition-all mt-6 relative overflow-hidden group/btn italic">
                        <span class="relative z-10">Enviar Instrucciones</span>
                        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover/btn:translate-y-0 transition-transform duration-500"></div>
                    </button>
                </form>
                @endif

                <!-- Footer Section -->
                <div class="mt-8 text-center border-t border-white/5 pt-8">
                    @if($mode === 'login')
                        <p class="text-[12px] text-white/20 font-black uppercase tracking-[0.2em]">
                            ¿Nuevo miembro?
                            <button type="button" wire:click="setMode('register')" class="text-agri-green font-black ml-2 hover:underline tracking-widest italic transition-all">Únete ahora</button>
                        </p>
                    @else
                        <p class="text-[12px] text-white/20 font-black uppercase tracking-[0.2em]">
                            ¿Ya tienes cuenta?
                            <button type="button" wire:click="setMode('login')" class="text-agri-green font-black ml-2 hover:underline tracking-widest italic transition-all">Inicia sesión</button>
                        </p>
                    @endif
                </div>
            </div>

            <!-- Features Icons (Positioned below Card for better Responsiveness) -->
            <div class="flex items-center justify-center space-x-10 mt-12 w-full select-none">
                <div class="flex flex-col items-center group">
                    <div class="w-14 h-14 rounded-3xl bg-white/5 backdrop-blur-3xl border border-white/10 flex items-center justify-center text-white text-xl group-hover:bg-agri-green group-hover:border-agri-green group-hover:scale-110 transition-all duration-700 shadow-2xl">
                        <i class="fa-solid fa-seedling"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase text-white/50 mt-4 tracking-[0.4em] group-hover:text-white transition-colors">Campo</span>
                </div>
                <div class="flex flex-col items-center group">
                    <div class="w-14 h-14 rounded-3xl bg-white/5 backdrop-blur-3xl border border-white/10 flex items-center justify-center text-white text-xl group-hover:bg-agri-green group-hover:border-agri-green group-hover:scale-110 transition-all duration-700 shadow-2xl">
                        <i class="fa-solid fa-microchip"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase text-white/50 mt-4 tracking-[0.4em] group-hover:text-white transition-colors">Tecnología</span>
                </div>
                <div class="flex flex-col items-center group">
                    <div class="w-14 h-14 rounded-3xl bg-white/5 backdrop-blur-3xl border border-white/10 flex items-center justify-center text-white text-xl group-hover:bg-agri-green group-hover:border-agri-green group-hover:scale-110 transition-all duration-700 shadow-2xl">
                        <i class="fa-solid fa-rocket"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase text-white/50 mt-4 tracking-[0.4em] group-hover:text-white transition-colors">Futuro</span>
                </div>
            </div>
        </div>
    </div>
</div>
