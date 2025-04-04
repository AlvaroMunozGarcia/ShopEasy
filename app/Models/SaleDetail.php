<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{

    protected $fillable=[
        'sale_id',
        'product_id',
        'sale_date',
        'tax',
        'total',
        'status',
    ];

    public function product(){
        return $this->belongsTo(Product::class);

    }

    /** @use HasFactory<\Database\Factories\SaleDetailFactory> */
    use HasFactory;
}
