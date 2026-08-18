<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Poblamos los roles globales y de organización
        $this->call([
            RolSeeder::class,
            RolesOrganizacionSeeder::class,
        ]);

        // 2. Creamos un Super Admin global para pruebas
        User::updateOrCreate(
            ['email' => 'admin@agrosys.com'],
            [
                'rol_id' => 1, // ID del Super Admin
                'nombres' => 'Super',
                'apellidos' => 'Administrador',
                'password' => bcrypt('password'),
                'dni' => '00000000',
                'is_activo' => 1,
            ]
        );
    }
}
