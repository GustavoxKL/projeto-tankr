<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'loginWeb'])->name('login.web');

Route::middleware(['auth'])->group(function() {
    Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout.web');
});

// Rotas ADMIN
Route::middleware(['auth', 'auth.admin'])->prefix('admin')->name('admin.')->group(function() {
    
    Route::get('/dashboard', function () {
        return view('admin/dashboard/dashboard_admin');
    })->name('dashboard.index');

    Route::get('/motoristas', [App\Http\Controllers\Admin\MotoristaViewController::class, 'index'])
        ->name('motoristas.index');

    Route::get('/veiculos', [App\Http\Controllers\Admin\VeiculoViewController::class, 'index'])
        ->name('veiculos.index');
    
    Route::get('/frentistas', [App\Http\Controllers\Admin\FrentistaViewController::class, 'index'])
        ->name('frentistas.index');

    /** 
    Route::get('/usuarios', [App\Http\Controllers\Admin\UsuarioViewController::class, 'index'])
        ->name('usuarios.index');
    
    Route::get('/estacoes', [App\Http\Controllers\Admin\EstacaoViewController::class, 'index'])
        ->name('estacoes.index');

    Route::get('/abastecimentos', [App\Http\Controllers\Admin\AbastecimentoViewController::class, 'index'])
        ->name('abastecimentos.index');
    */
});

// Rotas SUPERADMIN
Route::middleware(['auth', 'auth.superadmin'])->prefix('superadmin')->name('superadmin.')->group(function() {

    Route::get('/dashboard', function () {
        return view('superadmin/dashboard/dashboard_superadmin');
    })->name('dashboard.index');

    Route::get('/empresas', [App\Http\Controllers\SuperAdmin\EmpresaViewController::class, 'index'])
        ->name('empresas.index');
    
    Route::get('/usuarios', [App\Http\Controllers\SuperAdmin\UsuarioViewController::class, 'index'])
        ->name('usuarios.index');

    Route::get('/motoristas', [App\Http\Controllers\SuperAdmin\MotoristaViewController::class, 'index'])
        ->name('motoristas.index');

    Route::get('/veiculos', [App\Http\Controllers\SuperAdmin\VeiculoViewController::class, 'index'])
        ->name('veiculos.index');

    Route::get('/estacoes', [App\Http\Controllers\SuperAdmin\EstacaoViewController::class, 'index'])
        ->name('estacoes.index');

    //Route::get('/abastecimentos', [App\Http\Controllers\SuperAdmin\AbastecimentoViewController::class, 'index'])->name('abastecimentos.index');
    //Route::get('/frentistas', [App\Http\Controllers\SuperAdmin\FrentistasViewController::class, 'index'])->name('frentistas.index');
    //Route::get('/tanques', [App\Http\Controllers\SuperAdmin\TanquesViewController::class, 'index'])->name('tanques.index');
});
