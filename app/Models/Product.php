<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Product extends Model
{

    protected $fillable =[
        'code',
        'name',
        'stock',
        'min_stock', 
        'image',
        'sell_price',
        'status',
        'category_id',
        'provider_id',

    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function provider(){
        return $this->belongsTo(Provider::class);
    }
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
}
