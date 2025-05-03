<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Printer; // Asegúrate de importar el modelo Printer

class PrinterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Crea un registro de impresora por defecto si no existe ninguno con id=1
        // Esto asegura que Printer::firstOrFail() siempre funcione en el controlador.
        Printer::firstOrCreate(
            ['id' => 1], // Criterio para buscar si ya existe (puedes cambiarlo si prefieres)
            ['name' => 'ImpresoraPredeterminada'] // Valores a usar si se crea nuevo
        );
    }
}