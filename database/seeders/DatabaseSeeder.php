<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Empresa;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Crear empresa por defecto
        $empresa = Empresa::create([
            'nombre' => 'Empresa Principal',
            'email' => 'info@empresa.com',
        ]);

        // Usuario admin
        User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('Admin_654*'),
            'is_admin' => true,
            'empresa_id' => $empresa->id,
        ]);

        // Usuario normal
        User::create([
            'name' => 'usuario',
            'email' => 'usuario@admin.com',
            'password' => bcrypt('password'),
            'empresa_id' => $empresa->id,
            'is_admin' => false,
        ]);

        $this->call([
            TaskSeeder::class,
        ]);
    }
}
