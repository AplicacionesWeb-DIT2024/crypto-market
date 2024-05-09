<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Movimiento;
use App\Models\Usuario;

class movimientoController extends Controller
{
    public function index()
    {
        $movimientos = Movimiento::all();

        return response()->json($movimientos, 200);
    }

    public function show($usuario_id)
    {
        $usuario = Usuario::find($usuario_id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $movimiento = Movimiento::where('usuario_id', $usuario_id)->get();

        return response()->json($movimiento, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Movimiento::rules());

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $usuario = Usuario::find($request->input('usuario_id'));
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $monto = $request->input('monto');

        if ($monto <= 0) {
            return response()->json(['message' => 'Monto debe ser mayor a 0'], 400);
        }

        $tipo = $request->input('tipo');

        if ($tipo == 'RETIRO') {
            $saldo = Movimiento::saldo($usuario->id);

            if ($saldo < $monto) {
                return response()->json(['message' => 'Saldo insuficiente'], 400);
            }
        }

        $movimiento = Movimiento::create($request->all());

        return response()->json($movimiento, 201);
    }

    public function saldo($usuario_id)
    {
        $usuario = Usuario::find($usuario_id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json(['saldo' => Movimiento::saldo($usuario_id)], 200);
    }
}
