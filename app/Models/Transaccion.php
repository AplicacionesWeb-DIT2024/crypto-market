<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Transaccion extends Model
{
    use HasFactory;

    protected $table = 'transacciones';

    protected $fillable = [
        'usuario_id',
        'cripto_id',
        'precio',
        'cantidad',
        'tipo'
    ];

    public static function rules(){
        return [
            'usuario_id' => 'required',
            'cripto_id' => 'required',
            'precio' => 'required',
            'cantidad' => 'required',
            'tipo' => [
                'required',
                Rule::in(['COMPRA', 'VENTA']),
            ],
        ];
    }
}
