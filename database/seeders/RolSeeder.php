<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        Rol::create([
            'nombre' => 'Super Admin',
            'descripcion' => 'Administrador global del sistema',
        ]);

        Rol::create([
            'nombre' => 'Agricultor',
            'descripcion' => 'Usuario estándar (Agricultor)',
        ]);
    }
}
