<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criptomoneda extends Model
{
    use HasFactory;

    protected $table = 'criptomonedas';

    protected $fillable = [
        'simbolo',
        'comision'
    ];

    public static function rules()
    {
        return [
            'simbolo' => 'required|string',
            'comision' => 'required|numeric|min:0'
        ];
    }
}
