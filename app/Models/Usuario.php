<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';

    // Campos que pueden ser alterados
    protected $fillable = [
        'username',
        'email',
        'password',
        'saldo',
    ];
}
