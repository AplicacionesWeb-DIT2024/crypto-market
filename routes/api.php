<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\usuarioController;
use App\Http\Controllers\api\movimientoController;
use App\Http\Controllers\api\criptomonedaController;
use App\Http\Controllers\api\transaccionController;


Route::get("/status", function(){

    // Obtener hora UTC-3
    $hora = new DateTime("now", new DateTimeZone('America/Argentina/Buenos_Aires'));

    // Crear un arreglo con la información
    $info = [
        "estado" => "ok",
        "hora" => $hora
    ];

    // Convertir el arreglo a un JSON
    $json = json_encode($info);

    // Devolver la respuesta
    return $json;
});

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
Route::get('/criptos/precio/{id}', [criptomonedaController::class, 'precio']);

// Transacciones
Route::post('/comprar', [transaccionController::class, 'comprar']);
Route::post('/vender', [transaccionController::class, 'vender']);
Route::get('/transacciones/{usuario_id}', [transaccionController::class, 'index']);
Route::get('/portfolio/{usuario_id}', [transaccionController::class, 'portfolio']);

// Rutas para los admin
Route::get('/admin/criptos', [criptomonedaController::class, 'admin_index']);
Route::get('/admin/criptos/{id}', [criptomonedaController::class, 'admin_show']);
Route::post('/admin/criptos/registrar', [criptomonedaController::class, 'admin_store']);
Route::patch('/admin/criptos/actualizar/{id}', [criptomonedaController::class, 'admin_update']);
Route::delete('/admin/criptos/eliminar/{id}', [criptomonedaController::class, 'admin_destroy']);
