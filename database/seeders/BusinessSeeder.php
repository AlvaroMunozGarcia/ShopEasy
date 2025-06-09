<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Business; 
use Illuminate\Support\Facades\DB; 

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        Business::create([
            'name'        => 'ShopEasy (Tu Nombre)',
            'description' => 'Tienda online creada con Laravel.',
            'logo'        => null, 
            'email'       => 'contacto@shopeasy.com',
            'address'     => 'Calle Principal 123, Ciudad',
            'ruc'         => '12345678901', 
        ]);
    }
}