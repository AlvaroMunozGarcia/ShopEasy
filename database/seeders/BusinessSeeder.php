<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Business; // <-- Asegúrate de importar el modelo Business
use Illuminate\Support\Facades\DB; // Opcional, pero útil si necesitas desactivar FK checks, etc.

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crea el registro único para la información del negocio
        // ¡Recuerda cambiar estos valores por los de tu negocio real!
        Business::create([
            'name'        => 'ShopEasy (Tu Nombre)',
            'description' => 'Tienda online creada con Laravel.',
            'logo'        => null, // Puedes poner una ruta por defecto si tienes un logo inicial
            'email'       => 'contacto@shopeasy.com',
            'address'     => 'Calle Principal 123, Ciudad',
            'ruc'         => '12345678901', // RUC o Identificación Fiscal
        ]);
    }
}