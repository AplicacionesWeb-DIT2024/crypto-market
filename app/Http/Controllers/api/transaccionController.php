<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Criptomoneda;
use App\Models\Movimiento;
use App\Models\Transaccion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class transaccionController extends Controller
{

    public function index($usuario_id) {
        $transacciones = Transaccion::where('usuario_id', $usuario_id)->with('cripto')->get();
    
        return response()->json($transacciones);
    }
    
    public function comprar(){

        $validator = Validator::make(request()->all(), [
            'user_id' => 'required',
            'cripto_id' => 'required',
            'precio' => 'required',
            'cantidad' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $precio = request('precio');
        $cantidad = request('cantidad');
        $user_id = request('user_id');
        $cripto_id = request('cripto_id');
        
        $importe_total = $precio * $cantidad;

        $usuario = Usuario::find($user_id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $cripto = Criptomoneda::find($cripto_id);
        if (!$cripto) {
            return response()->json([
                'message' => 'Criptomoneda no encontrada'
            ], 404);
        }

        $saldo = Movimiento::saldo($user_id);

        // Verifico si el usuario tiene saldo suficiente
        if ($saldo < $importe_total) {
            return response()->json([
                'message' => 'Saldo insuficiente para realizar la compra. El usuario tiene $'.$saldo.' y el importe total es $'.$importe_total
            ], 400);
        }

        // Creo la transaccion de compra
        $transaccion = new Transaccion();
        $transaccion->usuario_id = $user_id;
        $transaccion->cripto_id = $cripto_id;
        $transaccion->precio = $precio;
        $transaccion->cantidad = $cantidad;
        $transaccion->tipo = 'COMPRA';
        $transaccion->save();

        $movimiento = new Movimiento();
        $movimiento->tipo = 'COMPRA';
        $movimiento->monto = $importe_total;
        $movimiento->fecha = date('Y-m-d H:i:s');
        $movimiento->usuario_id = $user_id;
        $movimiento->save();

        // Retorno un mensaje con todos los datos
        return response()->json([
            'message' => 'Compra realizada con éxito',
            'transaccion' => $transaccion,
            'movimiento' => $movimiento
        ]);
    }

    public function vender(){
        $validator = Validator::make(request()->all(), [
            'user_id' => 'required',
            'cripto_id' => 'required',
            'precio' => 'required',
            'cantidad' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $precio = request('precio');
        $cantidad = request('cantidad');
        $user_id = request('user_id');
        $cripto_id = request('cripto_id');

        $usuario = Usuario::find($user_id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $cripto = Criptomoneda::find($cripto_id);
        if (!$cripto) {
            return response()->json([
                'message' => 'Criptomoneda no encontrada'
            ], 404);
        }

        $saldo_cripto = Transaccion::saldo_cripto($user_id, $cripto_id);

        // Verifico si el usuario tiene saldo suficiente
        if ($saldo_cripto < $cantidad) {
            return response()->json([
                'message' => 'Saldo insuficiente para realizar la compra. El usuario tiene $'.$saldo_cripto.' y quiere vender $'.$cantidad
            ], 400);
        }

        // Creo la transaccion de compra
        $transaccion = new Transaccion();
        $transaccion->usuario_id = $user_id;
        $transaccion->cripto_id = $cripto_id;
        $transaccion->precio = $precio;
        $transaccion->cantidad = $cantidad;
        $transaccion->tipo = 'VENTA';
        $transaccion->save();

        $movimiento = new Movimiento();
        $movimiento->tipo = 'VENTA';
        $movimiento->monto = $cantidad * $precio;
        $movimiento->fecha = date('Y-m-d H:i:s');
        $movimiento->usuario_id = $user_id;
        $movimiento->save();

        // Retorno un mensaje con todos los datos
        return response()->json([
            'message' => 'Venta realizada con éxito',
            'transaccion' => $transaccion,
            'movimiento' => $movimiento
        ]);

    }
}
