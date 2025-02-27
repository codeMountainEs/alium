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
// Crear primera empresa si no existe
$empresa1 = Empresa::firstOrCreate(
    ['nombre' => 'Rotuleon'],
    [
        'email' => 'rotuleon@alium.com',
        'telefono' => '912345678'
    ]
);

// Crear segunda empresa
$empresa2 = Empresa::firstOrCreate(
    ['nombre' => 'Barcelona'],
    [
        'email' => 'barcelona@alium.com',
        'telefono' => '913456789'
    ]
);

        // Usuario admin
        User::create([
            'name' => 'admin',
            'email' => 'admin@alium.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'empresa_id' => $empresa1->id,
        ]);

        // Usuario normal
        User::create([
            'name' => 'barcelona',
            'email' => 'barcelona@alium.com',
            'password' => bcrypt('password'),
            'empresa_id' => $empresa2->id,
            'is_admin' => false,
        ]);
        User::create([
            'name' => 'rotuleon',
            'email' => 'rotuleon@alium.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'empresa_id' => $empresa1->id,
        ]);


        $this->call([
            TaskSeeder::class,
        ]);
    }
}
