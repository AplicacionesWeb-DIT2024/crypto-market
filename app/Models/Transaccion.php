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

    public function cripto()
    {
        return $this->belongsTo(Criptomoneda::class, 'cripto_id');
    }

    public static function saldo_cripto($usuario_id, $cripto_id) {
        $compras = Transaccion::where('usuario_id', $usuario_id)
            ->where('cripto_id', $cripto_id)
            ->where('tipo', 'COMPRA')
            ->sum('cantidad');

        $ventas = Transaccion::where('usuario_id', $usuario_id)
            ->where('cripto_id', $cripto_id)
            ->where('tipo', 'VENTA')
            ->sum('cantidad');

        return $compras - $ventas;
    }

    public static function get_portfolio($usuario_id) {
        $criptos = Criptomoneda::all();
        $portfolio = [];

        foreach ($criptos as $cripto) {
            $saldo = Transaccion::saldo_cripto($usuario_id, $cripto->id);

            if ($saldo > 0) {
                $portfolio[] = [
                    'cripto' => $cripto,
                    'saldo' => $saldo
                ];
            }
        }

        return $portfolio;
    }
}
