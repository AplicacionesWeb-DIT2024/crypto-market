<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Usuario;

class usuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::all();

        return response()->json($usuarios, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:usuarios',
            'email' => 'required|email|unique:usuarios',
            'password' => 'required|string',
            'saldo' => 'required|numeric',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $usuario = Usuario::create([
            'username' => $request->username,
            'email' => $request->email,
            'saldo' => $request->saldo,
            'password' => bcrypt($request->password),
        ]);


        if (!$usuario) {
            return response()->json([
                'message' => 'Error al crear el usuario',
            ], 500);
        }

        // TODO: ver de no retornar la contraseña (de todos modos está hasheada)
        return response()->json([
            'message' => 'Usuario creado correctamente',
            'usuario' => $usuario,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'string|unique:usuarios',
            'email' => 'email|unique:usuarios',
            'password' => 'string',
            'saldo' => 'numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $usuario->username = $request->username ?? $usuario->username;
        $usuario->email = $request->email ?? $usuario->email;
        $usuario->password = $request->password ? bcrypt($request->password) : $usuario->password;
        $usuario->saldo = $request->saldo ?? $usuario->saldo;

        if (!$usuario->save()) {
            return response()->json([
                'message' => 'Error al actualizar el usuario',
            ], 500);
        }

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'usuario' => $usuario,
        ], 200);
    }

    public function destroy($id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado',
            ], 404);
        }

        if (!$usuario->delete()) {
            return response()->json([
                'message' => 'Error al eliminar el usuario',
            ], 500);
        }

        return response()->json([
            'message' => 'Usuario eliminado correctamente',
        ], 200);
    }

    public function show($id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado',
            ], 404);
        }

        return response()->json($usuario, 200);
    }
}
