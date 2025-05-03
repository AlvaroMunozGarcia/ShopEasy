<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(BusinessSeeder::class); // <-- Esta línea ya está
        $this->call(PrinterSeeder::class); // <<<--- Añade esta línea
        // Llama al Seeder de Roles y Permisos PRIMERO
        $this->call([
            RolesAndPermissionsSeeder::class,
            // Aquí puedes añadir llamadas a otros Seeders si los tienes
            // CategorySeeder::class,
            // ProductSeeder::class,
            // etc.
        ]);

        // Ya no es necesario crear el usuario aquí si lo haces en RolesAndPermissionsSeeder
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
