<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{

    protected $fillable =['name','email','ruc_number','address','phone'];
    /** @use HasFactory<\Database\Factories\ProviderFactory> */
    use HasFactory;
}
