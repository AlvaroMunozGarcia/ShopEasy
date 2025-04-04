<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    

    protected $fillable=['name','dni','ruc','address','phone','email'];
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;
}
