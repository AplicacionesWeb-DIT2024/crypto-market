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
                Rule::in(['DEPOSITO', 'RETIRO', 'COMPRA', 'VENTA']),
            ],
            'monto' => 'required|numeric',
            'fecha' => 'required|date',
            'usuario_id' => 'required|exists:usuarios,id',
        ];
    }

    // Funcion para obtener el saldo actual de un usuario
    public static function saldo($usuario_id)
    {
        $ingresos = Movimiento::where('usuario_id', $usuario_id)
            ->whereIn('tipo', ['DEPOSITO', 'VENTA'])
            ->sum('monto');

        $egresos = Movimiento::where('usuario_id', $usuario_id)
            ->whereIn('tipo', ['RETIRO', 'COMPRA'])
            ->sum('monto');

        return $ingresos - $egresos;
    }
}
