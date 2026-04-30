<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EstacaoAbastecimentoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\MotoristaController;
use App\Http\Controllers\VeiculoController;

// Rota de teste
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API funcionando!'
    ]);
});

Route::apiResource('empresas', EmpresaController::class);
Route::apiResource('estacoes', EstacaoAbastecimentoController::class)->parameters(['estacoes' => 'estacao']);
Route::apiResource('motoristas', MotoristaController::class);
Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('veiculos', VeiculoController::class);

// ==================== ROTAS PÚBLICAS ====================

// Autenticação
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// ==================== ROTAS PROTEGIDAS ====================

Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    //Route::post('/auth/logout', [AuthController::class, 'logout']);
    //Route::get('/auth/me', [AuthController::class, 'me']);
    //Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
    
    // Empresas
    //Route::apiResource('empresas', EmpresaController::class);
    
    // Usuários
    //Route::apiResource('usuarios', UsuarioController::class);
    
    // Motoristas
    //Route::apiResource('motoristas', MotoristaController::class);
    
    // Veículos
    //Route::apiResource('veiculos', VeiculoController::class);
    //Route::get('/veiculos/buscar/{codigo}', [VeiculoController::class, 'buscarPorCodigo']);
});

