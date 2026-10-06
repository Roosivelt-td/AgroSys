<div x-data="{ showPassword: false }">
    <div class="text-[#173B27] bg-white font-sans">

        <style>
            .container-main {
                width: min(1180px, calc(100% - 40px));
                margin: auto;
            }
            .hero-grid {
                background-image:
                    linear-gradient(rgba(23,59,39,.045) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(23,59,39,.045) 1px, transparent 1px);
                background-size: 48px 48px;
            }
            .auth-card {
                transition: transform .35s ease, box-shadow .35s ease;
            }
            .auth-card:hover {
                box-shadow: 0 25px 70px rgba(23,59,39,.12);
            }
        </style>

        <!-- =========================================================
             HERO / AUTH SECTION
        ========================================================= -->
        <section class="pt-12 pb-20 bg-[#F7F9F5] hero-grid overflow-hidden min-h-[calc(100vh-82px)] flex items-center">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 py-8 w-full">

                <div class="grid lg:grid-cols-12 gap-12 items-center">

                    <!-- LEFT PANEL: Hero Info Showcase -->
                    <div class="lg:col-span-5 space-y-6">
                        <div class="inline-flex items-center gap-2 bg-white rounded-full px-4 py-2 shadow-sm border border-gray-100">
                            <img src="{{ asset('AgroSys_logo.png') }}" alt="AgroSys Logo" class="w-4 h-4 object-contain inline-block mr-1 align-middle">
                            <span class="text-xs font-bold uppercase tracking-[2px] text-[#173B27]">Acceso a la plataforma</span>
                        </div>

                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight tracking-tight text-[#173B27]">
                            Potencia la gestión de tu campo con <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span>
                        </h1>

                        <p class="text-lg text-[#5E665F] leading-8">
                            Ingresa o crea tu cuenta para acceder a la tecnología de monitoreo satelital, análisis agronómico y control total de tus terrenos y cultivos.
                        </p>

                        <!-- Feature highlights -->
                        <div class="space-y-4 pt-4 border-t border-gray-200/60">
                            <div class="flex items-center gap-3 text-sm font-semibold text-[#173B27]">
                                <div class="w-7 h-7 rounded-full bg-[#e8f1dc] text-[#78B82A] flex items-center justify-center font-bold">✓</div>
                                <span>Monitoreo satelital en tiempo real</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm font-semibold text-[#173B27]">
                                <div class="w-7 h-7 rounded-full bg-[#e8f1dc] text-[#78B82A] flex items-center justify-center font-bold">✓</div>
                                <span>Modelos predictivos de riego y plagas</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm font-semibold text-[#173B27]">
                                <div class="w-7 h-7 rounded-full bg-[#e8f1dc] text-[#78B82A] flex items-center justify-center font-bold">✓</div>
                                <span>Cuaderno de campo digital y trazabilidad</span>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT PANEL: Livewire Auth Card -->
                    <div class="lg:col-span-7 bg-white rounded-[32px] p-8 md:p-12 shadow-xl border border-gray-100 auth-card">

                        <!-- Mode Switcher Tabs -->
                        @if($mode !== 'waiting_authorization')
                        <div class="flex items-center justify-center bg-[#F7F9F5] p-1.5 rounded-full border border-gray-100 mb-8 max-w-sm mx-auto w-full">
                            <button type="button"
                                    wire:click="setMode('login')"
                                    class="flex-1 py-3 rounded-full text-xs font-extrabold uppercase tracking-wider transition-all duration-300 {{ $mode === 'login' ? 'bg-[#173B27] text-white shadow-md' : 'text-gray-500 hover:text-[#173B27]' }}">
                                Iniciar sesión
                            </button>
                            <button type="button"
                                    wire:click="setMode('register')"
                                    class="flex-1 py-3 rounded-full text-xs font-extrabold uppercase tracking-wider transition-all duration-300 {{ $mode === 'register' ? 'bg-[#173B27] text-white shadow-md' : 'text-gray-500 hover:text-[#173B27]' }}">
                                Registrarse
                            </button>
                        </div>
                        @endif

                        <x-auth-session-status class="mb-6" :status="session('status')" />

                        <!-- LOGIN MODE -->
                        @if($mode === 'login')
                        <form wire:submit="login" class="space-y-5 max-w-md mx-auto w-full">
                            <div class="text-center mb-4 space-y-1">
                                <h2 class="text-2xl font-bold text-[#173B27]">Bienvenido de nuevo</h2>
                                <p class="text-xs text-[#5E665F]">Ingresa tus credenciales para acceder a la plataforma</p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-xs font-bold text-[#173B27] uppercase tracking-wider mb-2">Correo electrónico</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                        <i class="fa-regular fa-envelope text-sm"></i>
                                    </div>
                                    <input wire:model="loginEmail"
                                           name="loginEmail"
                                           type="email"
                                           required
                                           autofocus
                                           autocomplete="username"
                                           placeholder="tu@email.com"
                                           class="w-full pl-11 pr-4 py-3.5 bg-[#F7F9F5] border border-gray-200 rounded-2xl text-sm font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#78B82A] focus:border-transparent transition">
                                </div>
                                <x-input-error :messages="$errors->get('loginEmail')" class="mt-1 text-xs" />
                            </div>

                            <!-- Password -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-xs font-bold text-[#173B27] uppercase tracking-wider">Contraseña</label>
                                    <button type="button" wire:click="setMode('forgot')" class="text-xs font-bold text-[#78B82A] hover:underline">
                                        ¿Olvidaste tu contraseña?
                                    </button>
                                </div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                        <i class="fa-solid fa-lock text-sm"></i>
                                    </div>
                                    <input wire:model="loginPassword"
                                           name="loginPassword"
                                           :type="showPassword ? 'text' : 'password'"
                                           required
                                           autocomplete="current-password"
                                           placeholder="••••••••"
                                           class="w-full pl-11 pr-11 py-3.5 bg-[#F7F9F5] border border-gray-200 rounded-2xl text-sm font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#78B82A] focus:border-transparent transition">
                                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                        <i class="fa-regular" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('loginPassword')" class="mt-1 text-xs" />
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full py-4 bg-[#173B27] hover:bg-[#245337] text-white rounded-full font-bold text-sm uppercase tracking-wider shadow-md transition transform hover:-translate-y-0.5 active:translate-y-0">
                                Iniciar sesión
                            </button>
                        </form>
                        @endif

                        <!-- WAITING AUTHORIZATION MODE -->
                        @if($mode === 'waiting_authorization')
                        <div wire:poll.2s="checkAuthorizationStatus" class="space-y-6 max-w-md mx-auto w-full text-center py-4">
                            <div class="w-16 h-16 rounded-full bg-amber-500/10 text-amber-600 flex items-center justify-center mx-auto text-3xl animate-pulse">
                                <i class="fa-solid fa-hourglass-half"></i>
                            </div>

                            <div class="space-y-2">
                                <h2 class="text-2xl font-bold text-[#173B27]">Sesión Activa Detectada</h2>
                                <p class="text-xs text-[#5E665F] leading-relaxed">
                                    Esta cuenta ya tiene una sesión abierta en otro dispositivo. Se ha enviado una solicitud de autorización al dispositivo activo.
                                </p>
                            </div>

                            <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 text-xs space-y-2 text-left">
                                <div class="flex items-center justify-between text-amber-800 font-bold">
                                    <span>Estado de la Solicitud:</span>
                                    <span class="inline-flex items-center gap-1.5 text-amber-600 font-black">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                        Esperando confirmación...
                                    </span>
                                </div>
                                <p class="text-[11px] text-amber-700 leading-normal">
                                    Por seguridad, el usuario en el dispositivo activo debe ingresar su contraseña para autorizar la transferencia de sesión.
                                </p>
                            </div>

                            <button type="button"
                                    wire:click="cancelWaitingAuthorization"
                                    class="w-full py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full font-bold text-xs uppercase tracking-wider transition">
                                Cancelar y volver
                            </button>
                        </div>
                        @endif

                        <!-- REGISTER MODE -->
                        @if($mode === 'register')
                        <form wire:submit="register" class="space-y-4 max-w-md mx-auto w-full">
                            <div class="text-center mb-4 space-y-1">
                                <h2 class="text-2xl font-bold text-[#173B27]">Crea tu cuenta en <span style="color: #0a5f18;">Agro</span><span style="color: #f54e05;">Sys</span></h2>
                                <p class="text-xs text-[#5E665F]">Únete para gestionar tus terrenos, cultivos y operaciones agrícolas</p>
                            </div>

                            <!-- Nombres y Apellidos -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-[#173B27] uppercase tracking-wider mb-1">Nombres</label>
                                    <input wire:model="nombres" type="text" required placeholder="Nombres"
                                           class="w-full px-4 py-3 bg-[#F7F9F5] border border-gray-200 rounded-2xl text-xs font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#78B82A]"
                                           oninput="this.value = this.value.replace(/[^a-zA-ZÁÉÍÓÚáéíóúÑñ ]/g, '').replace(/(\s{2,})/g, ' ')">
                                    <x-input-error :messages="$errors->get('nombres')" class="mt-1 text-xs" />
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-[#173B27] uppercase tracking-wider mb-1">Apellidos</label>
                                    <input wire:model="apellidos" type="text" required placeholder="Apellidos"
                                           class="w-full px-4 py-3 bg-[#F7F9F5] border border-gray-200 rounded-2xl text-xs font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#78B82A]"
                                           oninput="this.value = this.value.replace(/[^a-zA-ZÁÉÍÓÚáéíóúÑñ ]/g, '').replace(/(\s{2,})/g, ' ')">
                                    <x-input-error :messages="$errors->get('apellidos')" class="mt-1 text-xs" />
                                </div>
                            </div>

                            <!-- DNI -->
                            <div>
                                <label class="block text-xs font-bold text-[#173B27] uppercase tracking-wider mb-1">DNI (8 dígitos)</label>
                                <input wire:model="dni" type="text" inputmode="numeric" maxlength="8" required placeholder="12345678"
                                       class="w-full px-4 py-3 bg-[#F7F9F5] border border-gray-200 rounded-2xl text-xs font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#78B82A]"
                                       onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                <x-input-error :messages="$errors->get('dni')" class="mt-1 text-xs" />
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-xs font-bold text-[#173B27] uppercase tracking-wider mb-1">Correo electrónico</label>
                                <input wire:model="email" type="email" required placeholder="tu@email.com"
                                       class="w-full px-4 py-3 bg-[#F7F9F5] border border-gray-200 rounded-2xl text-xs font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#78B82A]">
                                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                            </div>

                            <!-- Contraseñas -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-[#173B27] uppercase tracking-wider mb-1">Contraseña</label>
                                    <input wire:model="password" type="password" required placeholder="••••••••"
                                           class="w-full px-4 py-3 bg-[#F7F9F5] border border-gray-200 rounded-2xl text-xs font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#78B82A]">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-[#173B27] uppercase tracking-wider mb-1">Confirmar</label>
                                    <input wire:model="password_confirmation" type="password" required placeholder="••••••••"
                                           class="w-full px-4 py-3 bg-[#F7F9F5] border border-gray-200 rounded-2xl text-xs font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#78B82A]">
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />

                            <!-- Submit Button -->
                            <button type="submit" class="w-full py-4 bg-[#78B82A] hover:bg-[#609B20] text-white rounded-full font-bold text-sm uppercase tracking-wider shadow-md transition transform hover:-translate-y-0.5 active:translate-y-0 mt-2">
                                Crear mi cuenta
                            </button>
                        </form>
                        @endif

                        <!-- FORGOT MODE -->
                        @if($mode === 'forgot')
                        <form wire:submit="sendResetLink" class="space-y-5 max-w-md mx-auto w-full">
                            <div class="text-center mb-4 space-y-1">
                                <h2 class="text-2xl font-bold text-[#173B27]">Recuperar contraseña</h2>
                                <p class="text-xs text-[#5E665F]">Ingresa tu correo y te enviaremos las instrucciones de acceso</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-[#173B27] uppercase tracking-wider mb-2">Correo registrado</label>
                                <input wire:model="forgotEmail" type="email" required placeholder="tu@email.com"
                                       class="w-full px-4 py-3.5 bg-[#F7F9F5] border border-gray-200 rounded-2xl text-sm font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#78B82A]">
                                <x-input-error :messages="$errors->get('forgotEmail')" class="mt-1 text-xs" />
                            </div>

                            <button type="submit" class="w-full py-4 bg-[#173B27] hover:bg-[#245337] text-white rounded-full font-bold text-sm uppercase tracking-wider shadow-md transition">
                                Enviar instrucciones
                            </button>

                            <div class="text-center pt-2">
                                <button type="button" wire:click="setMode('login')" class="text-xs font-bold text-[#78B82A] hover:underline">
                                    ← Volver a Iniciar sesión
                                </button>
                            </div>
                        </form>
                        @endif

                    </div>

                </div>

            </div>
        </section>

    </div>
</div>
