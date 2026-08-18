<?php

namespace App\Livewire\Organizaciones;

use App\Http\Controllers\OrganizacionController;
use Livewire\Component;

/**
 * LÓGICA (BACKEND) - Componente para crear organizaciones.
 * Aquí reside toda la inteligencia y validación.
 */
class CrearForm extends Component
{
    // Propiedades del formulario
    public string $nombre = '';
    public string $descripcion = '';
    public string $ruc = '';
    public string $telefono = '';
    public string $email = '';
    public string $direccion = '';

    /**
     * Reglas de validación.
     */
    protected $rules = [
        'nombre' => 'required|string|max:150',
        'descripcion' => 'nullable|string|max:500',
        'ruc' => 'required|string|max:20|unique:organizaciones,ruc',
        'telefono' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'direccion' => 'nullable|string|max:255',
    ];

    /**
     * Ejecuta el guardado llamando al Controlador MVC.
     */
    public function save()
    {
        $validatedData = $this->validate();

        // Llamamos al Backend (Controlador)
        $controller = new OrganizacionController();
        $resultado = $controller->registrar($validatedData);

        if ($resultado['success']) {
            session()->flash('status', $resultado['mensaje']);
            $this->dispatch('close-modal', 'create-organization');
            return redirect()->route('dashboard');
        }
    }

    /**
     * Renderiza la vista (Solo indica qué archivo usar).
     */
    public function render()
    {
        return view('livewire.organizaciones.crear-form');
    }
}
