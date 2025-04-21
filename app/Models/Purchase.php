<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable =[
        'provider_id',
        'user_id',
        'purchase_date',
        'tax',
        'total',
        'status',
        'picture',

    ];

    protected $casts = [
        'purchase_date' => 'datetime', // ¡Esta es la línea clave!
        // 'total' => 'decimal:2', // Ejemplo: podrías castear el total también si es necesario
        // 'tax' => 'float',       // Ejemplo
    ];



    public function user(){
        return $this->belongsTo(User::class);
    }
    public function provider(){
        return $this->belongsTo(Provider::class);
    }
    public function purchaseDetails(){
        return $this->hasMany(PurchaseDetails::class);
    }

    /** @use HasFactory<\Database\Factories\PurchaseFactory> */
    use HasFactory;
}
