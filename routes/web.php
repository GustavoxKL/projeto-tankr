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

    // Rotas Admin
    Route::get('/admin/dashboard', function () {
        return view('admin/dashboard/dashboard_admin');
    })->name('admin.dashboard.index');
    
    /** 
    Route::get('/admin/usuarios', [App\Http\Controllers\Admin\UsuarioViewController::class, 'index'])
        ->name('admin.usuarios.index');
    
    Route::get('/admin/motoristas', [App\Http\Controllers\Admin\MotoristaViewController::class, 'index'])
        ->name('admin.motoristas.index');

    Route::get('/admin/veiculos', [App\Http\Controllers\Admin\VeiculoViewController::class, 'index'])
        ->name('admin.veiculos.index');
    
    Route::get('/admin/estacoes', [App\Http\Controllers\Admin\EstacaoViewController::class, 'index'])
        ->name('admin.estacoes.index');

    Route::get('/admin/abastecimentos', [App\Http\Controllers\Admin\AbastecimentoViewController::class, 'index'])
        ->name('admin.abastecimentos.index');
    */

        
    // Rotas SuperAdmin
    Route::get('/superadmin/dashboard', function () {
        return view('superadmin/dashboard/dashboard_superadmin');
    })->name('superadmin.dashboard.index');

    Route::get('/superadmin/empresas', [App\Http\Controllers\SuperAdmin\EmpresaViewController::class, 'index'])
        ->name('superadmin.empresas.index');
    
    Route::get('/superadmin/usuarios', [App\Http\Controllers\SuperAdmin\UsuarioViewController::class, 'index'])
        ->name('superadmin.usuarios.index');

    Route::get('/superadmin/motoristas', [App\Http\Controllers\SuperAdmin\MotoristaViewController::class, 'index'])
        ->name('superadmin.motoristas.index');

    Route::get('/superadmin/veiculos', [App\Http\Controllers\SuperAdmin\VeiculoViewController::class, 'index'])
        ->name('superadmin.veiculos.index');

    Route::get('/superadmin/estacoes', [App\Http\Controllers\SuperAdmin\EstacaoViewController::class, 'index'])
        ->name('superadmin.estacoes.index');

    //Route::get('/superadmin/abastecimentos', [App\Http\Controllers\SuperAdmin\AbastecimentoViewController::class, 'index'])->name('superadmin.abastecimentos.index');
    
    
    Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout.web');
});
