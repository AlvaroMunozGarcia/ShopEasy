<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 
use App\Models\Sale; 

class Client extends Model
{
    use HasFactory, SoftDeletes; 

    protected $fillable=['name','dni','ruc','address','phone','email'];

    public function sales()
    {
        return $this->hasMany(Sale::class); 
    }

}
