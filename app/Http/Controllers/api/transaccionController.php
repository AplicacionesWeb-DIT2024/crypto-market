<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Movimiento;
use App\Models\Usuario;
use Illuminate\Http\Request;

class transaccionController extends Controller
{
    
    public function comprar(){

        // Obtengo los datos de la petición
        $datos = request()->all();
        $user_id = $datos['user_id'] ?? null;
        $cripto_id = $datos['cripto_id'] ?? null;
        $precio = $datos['precio'] ?? null;
        $cantidad = $datos['cantidad'] ?? null;

        if ($user_id === null || $cripto_id === null || $precio === null || $cantidad === null) {
            return response()->json([
            'message' => 'Faltan datos requeridos para realizar la compra.'
            ], 400);
        }
        $importe_total = $precio * $cantidad;

        // Obtengo el usuario
        $usuario = Usuario::find($user_id);
        $saldo = Movimiento::saldo($user_id);

        // Verifico si el usuario tiene saldo suficiente
        if ($saldo < $importe_total) {
            return response()->json([
                'message' => 'Saldo insuficiente para realizar la compra. El usuario tiene $'.$saldo.' y el importe total es $'.$importe_total
            ], 400);
        }

        // TODO: revisar

        return response()->json([
            'message' => 'Se ha comprado '. $cantidad . ' de ' . $cripto_id . ' a '.$precio
        ]);
    }

    public function vender(){
        return response()->json([
            'message' => 'Venta realizada con éxito'
        ]);
    }
}
