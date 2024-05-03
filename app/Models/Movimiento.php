<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Movimiento extends Model
{
    use HasFactory;

    protected $table = 'movimientos';

    protected $fillable = [
        'tipo',
        'monto',
        'fecha',
        'usuario_id',
    ];

    public static function rules()
    {
        return [
            'tipo' => [
                'required',
                Rule::in(['DEPOSITO', 'RETIRO']),
            ],
            'monto' => 'required|numeric',
            'fecha' => 'required|date',
            'usuario_id' => 'required|exists:usuarios,id',
        ];
    }

    // Funcion para obtener el saldo actual de un usuario
    public static function saldo($usuario_id)
    {
        $depositos = Movimiento::where('usuario_id', $usuario_id)
            ->where('tipo', 'DEPOSITO')
            ->sum('monto');

        $retiros = Movimiento::where('usuario_id', $usuario_id)
            ->where('tipo', 'RETIRO')
            ->sum('monto');

        return $depositos - $retiros;
    }
}
