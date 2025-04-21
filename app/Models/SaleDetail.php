<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity', // <-- Necesario
        'price',    // <-- Necesario
        'discount', // <-- Necesario (si usas descuento por línea)
    ];
    public function product(){
        return $this->belongsTo(Product::class);

    }

    /** @use HasFactory<\Database\Factories\SaleDetailFactory> */
    use HasFactory;
}
