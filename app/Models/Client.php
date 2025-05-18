<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    
    protected $fillable=['name','dni','ruc','address','phone','email'];

    // Relación uno a muchos: Un cliente puede tener muchas ventas
    public function sales()
    {
        return $this->hasMany(\App\Models\Sale::class); // Asegúrate de que la ruta a tu modelo Sale sea correcta
    }

    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;
}
