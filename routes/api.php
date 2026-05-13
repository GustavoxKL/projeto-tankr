<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EstacaoAbastecimentoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\MotoristaController;
use App\Http\Controllers\VeiculoController;

Route::apiResource('empresas', EmpresaController::class);
Route::apiResource('estacoes', EstacaoAbastecimentoController::class)->parameters(['estacoes' => 'estacao']);
Route::apiResource('motoristas', MotoristaController::class);
Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('veiculos', VeiculoController::class);


// ==================== ROTAS PÚBLICAS ====================
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/sla', function () {
        return response()->json([
            'success' => true,
            'message' => 'API funcionando!'
        ]);
    });
    
    Route::get('/me', function (Request $request) {
        return response()->json([
            'user' => $request->user()
        ]);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});


//Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    //Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
    
    // Empresas
    //Route::apiResource('empresas', EmpresaController::class);
    
    // Usuários
    //Route::apiResource('usuarios', UsuarioController::class);
    
    // Motoristas
    //Route::apiResource('motoristas', MotoristaController::class);
    
    // Veículos
    //Route::apiResource('veiculos', VeiculoController::class);
//});

