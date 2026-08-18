<div class="space-y-6">
    <!-- VISTA (FRONTEND) - Verificar Email -->
    <div class="mb-4 text-sm text-slate-600">
        {{ __('¡Gracias por registrarse! Antes de comenzar, ¿podría verificar su dirección de correo electrónico haciendo clic en el enlace que le acabamos de enviar?') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('Se ha enviado un nuevo enlace de verificación a la dirección de correo electrónico que proporcionó durante el registro.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <button wire:click="sendVerification" class="px-4 py-2 bg-agri-green text-white rounded-lg font-bold text-xs uppercase tracking-widest hover:bg-agri-forest transition-all">
            {{ __('Re-enviar Email de Verificación') }}
        </button>

        <button wire:click="logout" type="submit" class="underline text-sm text-slate-600 hover:text-slate-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ __('Cerrar Sesión') }}
        </button>
    </div>
</div>
