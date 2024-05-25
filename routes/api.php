<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\usuarioController;
use App\Http\Controllers\api\movimientoController;
use App\Http\Controllers\api\criptomonedaController;
use App\Http\Controllers\api\transaccionController;
use App\Http\Controllers\api\comentarioController;

//  Usuarios
Route::get('/usuarios', [usuarioController::class, 'index']);
Route::get('/usuarios/{id}', [usuarioController::class, 'show']);
Route::delete('/usuarios/eliminar/{id}', [usuarioController::class, 'destroy']);
Route::post('/usuarios/registrar', [usuarioController::class, 'store']);
Route::patch('/usuarios/actualizar/{id}', [usuarioController::class, 'update']);

Route::post('/login', [usuarioController::class, 'login']);

// Movimientos
Route::get('/movimientos', [movimientoController::class, 'index']);
Route::get('/movimientos/{usuario_id}', [movimientoController::class, 'show']);
Route::post('/movimientos/registrar', [movimientoController::class, 'store']);
Route::get('/saldo/{usuario_id}', [movimientoController::class, 'saldo']);

// Criptomonedas
Route::get('/criptos', [criptomonedaController::class, 'index']);
Route::get('/criptos/{id}', [criptomonedaController::class, 'show']);

// Transacciones
Route::post('/comprar', [transaccionController::class, 'comprar']);
Route::post('/vender', [transaccionController::class, 'vender']);
Route::get('/transacciones/{usuario_id}', [transaccionController::class, 'index']);
Route::get('/portfolio/{usuario_id}', [transaccionController::class, 'portfolio']);

// Comentarios
// Route::get('/criptos/comentarios/{cripto_id}', [comentarioController::class, 'index']);
// Route::post('/criptos/comentarios/{cripto_id}', [comentarioController::class, 'store']);
