<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RolesOrganizacion;

class RolesOrganizacionSeeder extends Seeder
{
    /**
     * Población de los cargos internos que existen dentro de una organización agrícola.
     */
    public function run(): void
    {
        RolesOrganizacion::create([
            'nombre' => 'Administrador',
            'descripcion' => 'Responsable total de la gestión de la organización, miembros y terrenos.',
        ]);

        RolesOrganizacion::create([
            'nombre' => 'Supervisor',
            'descripcion' => 'Encargado de monitorear y asignar tareas a los agricultores bajo su cargo.',
        ]);

        RolesOrganizacion::create([
            'nombre' => 'Agricultor',
            'descripcion' => 'Personal operativo encargado de los cultivos y labores en campo.',
        ]);

        RolesOrganizacion::create([
            'nombre' => 'Agricultor_Grado_1',
            'descripcion' => 'Agricultor con experiencia básica o en entrenamiento.',
        ]);
    }
}
