<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable=[
        'client_id',
        'user_id',
        'sale_date',
        'tax',
        'total',
        'status',
    ];

    protected $casts = [
        'sale_date' => 'datetime', 
        'total' => 'decimal:2', 
        'tax' => 'decimal:2',   
    ];



    public function user(){
        return $this->belongsTo(User::class);
    }
    public function client(){
        return $this->belongsTo(Client::class);
    }
    public function saleDetails(){
        return $this->hasMany(SaleDetail::class);
    }

    /** @use HasFactory<\Database\Factories\SaleFactory> */
    use HasFactory;
}
