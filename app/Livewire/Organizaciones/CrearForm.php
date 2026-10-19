<?php

namespace App\Livewire\Organizaciones;

use App\Http\Controllers\OrganizacionController;
use Livewire\Component;

/**
 * LÓGICA (BACKEND) - Componente para crear organizaciones y procesar pago simulado con tarjeta.
 */
class CrearForm extends Component
{
    // Paso del asistente (1 = Datos de Empresa + Plan, 2 = Pasarela de Pago)
    public int $step = 1;

    // Propiedades de la Empresa (Paso 1)
    public string $nombre = '';
    public string $descripcion = '';
    public string $ruc = '';
    public string $telefono = '';
    public string $email = '';
    public string $direccion = '';
    public int $anios_suscripcion = 1;
    public float $tarifa_anual = 350.00;

    // Propiedades del Pago con Tarjeta (Paso 2)
    public string $tarjeta_numero = '';
    public string $tarjeta_titular = '';
    public string $tarjeta_expiracion = ''; // MM/YY
    public string $tarjeta_cvv = '';
    public bool $procesandoPago = false;

    /**
     * Valida el Paso 1 y avanza al Paso 2 (Pasarela de Pago).
     */
    public function irAPagar()
    {
        $this->validate([
            'nombre' => 'required|string|min:3|max:150',
            'ruc' => 'required|string|digits:11|unique:organizaciones,ruc',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'anios_suscripcion' => 'required|integer|min:1|max:10',
        ], [
            'nombre.required' => 'El nombre de la empresa es obligatorio.',
            'nombre.min' => 'El nombre debe contener al menos 3 caracteres.',
            'ruc.required' => 'El número de RUC es obligatorio.',
            'ruc.digits' => 'El RUC debe constar de 11 dígitos numéricos.',
            'ruc.unique' => 'Este número de RUC ya se encuentra registrado en el sistema.',
            'anios_suscripcion.required' => 'Seleccione los años de suscripción.',
        ]);

        $this->step = 2;
    }

    /**
     * Regresa al Paso 1 para modificar datos de la empresa o cambiar de plan.
     */
    public function volverADatos()
    {
        $this->step = 1;
    }

    /**
     * Valida estrictamente la tarjeta de crédito/débito y ejecuta el pago e inscripción.
     */
    public function procesarPago()
    {
        $numLimpio = preg_replace('/\D/', '', $this->tarjeta_numero);
        $expLimpia = preg_replace('/\D/', '', $this->tarjeta_expiracion);

        $this->validate([
            'tarjeta_numero' => [
                'required',
                function ($attribute, $value, $fail) use ($numLimpio) {
                    if (strlen($numLimpio) !== 16) {
                        $fail('El número de tarjeta debe contener exactamente 16 dígitos numéricos.');
                    }
                },
            ],
            'tarjeta_titular' => 'required|string|min:3|max:100',
            'tarjeta_expiracion' => [
                'required',
                'regex:/^(0[1-9]|1[0-2])\/?([0-9]{2})$/',
                function ($attribute, $value, $fail) use ($expLimpia) {
                    if (strlen($expLimpia) === 4) {
                        $mes = (int)substr($expLimpia, 0, 2);
                        $anio = (int)substr($expLimpia, 2, 2);
                        $anioActual = (int)now()->format('y');
                        $mesActual = (int)now()->format('m');

                        if ($mes < 1 || $mes > 12) {
                            $fail('El mes de expiración debe estar entre 01 y 12.');
                        } elseif ($anio < $anioActual || ($anio === $anioActual && $mes < $mesActual)) {
                            $fail('La tarjeta ingresada se encuentra vencida. Ingrese una fecha de expiración futura.');
                        }
                    } else {
                        $fail('La fecha de expiración debe ser en formato MM/AA (Ej. 08/28).');
                    }
                },
            ],
            'tarjeta_cvv' => 'required|numeric|digits:3',
        ], [
            'tarjeta_numero.required' => 'Ingrese el número de la tarjeta de crédito o débito.',
            'tarjeta_titular.required' => 'Ingrese el nombre del titular como figura en la tarjeta.',
            'tarjeta_titular.min' => 'El nombre del titular debe tener al menos 3 caracteres.',
            'tarjeta_expiracion.required' => 'Ingrese la fecha de vencimiento de la tarjeta (MM/AA).',
            'tarjeta_expiracion.regex' => 'Formato de fecha inválido. Utilice el formato MM/AA (Ej. 08/28).',
            'tarjeta_cvv.required' => 'Ingrese el código de seguridad CVV.',
            'tarjeta_cvv.digits' => 'El código CVV debe tener exactamente 3 dígitos.',
        ]);

        $this->procesandoPago = true;

        $ultimos4 = substr($numLimpio, -4);
        $marcaTarjeta = str_starts_with($numLimpio, '4') ? 'Visa' : (str_starts_with($numLimpio, '5') ? 'Mastercard' : 'Tarjeta');

        $datosFinales = [
            'nombre' => $this->nombre,
            'ruc' => $this->ruc,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'direccion' => $this->direccion,
            'descripcion' => $this->descripcion,
            'anios_suscripcion' => $this->anios_suscripcion,
            'tarifa_anual' => $this->tarifa_anual,
            'transaccion_id' => 'TXN-AGRO-' . rand(100000, 999999),
            'tarjeta_resumen' => "{$marcaTarjeta} **** {$ultimos4}",
            'tarjeta_titular' => strtoupper($this->tarjeta_titular),
        ];

        // Llamamos al Controlador MVC
        $controller = new OrganizacionController();
        $resultado = $controller->registrar($datosFinales);

        $this->procesandoPago = false;

        if ($resultado['success']) {
            session()->flash('status', $resultado['mensaje']);
            $this->dispatch('close-modal', 'create-organization');
            return redirect()->route('dashboard');
        }
    }

    /**
     * Renderiza la vista del componente.
     */
    public function render()
    {
        return view('livewire.organizaciones.crear-form');
    }
}
