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
    Route::get('/dashboard-admin', function () {
        return view('dashboard_admin');
    })->name('dashboard.admin');
    
    /** 
    Route::get('/admin/usuarios', [App\Http\Controllers\SuperAdmin\EmpresaViewController::class, 'index'])
        ->name('admin.usuarios.index');
    
    Route::get('/admin/motoristas', [App\Http\Controllers\SuperAdmin\EmpresaViewController::class, 'index'])
        ->name('admin.motoristas.index');

    Route::get('/admin/veiculos', [App\Http\Controllers\SuperAdmin\EmpresaViewController::class, 'index'])
        ->name('admin.veiculos.index');

    Route::get('/admin/abastecimentos', [App\Http\Controllers\SuperAdmin\EmpresaViewController::class, 'index'])
        ->name('admin.abastecimentos.index');
    */

    // Rotas SuperAdmin
    Route::get('/dashboard-superadmin', function () {
        return view('dashboard_superadmin');
    })->name('dashboard.superadmin');

    Route::get('/superadmin/empresas', [App\Http\Controllers\SuperAdmin\EmpresaViewController::class, 'index'])
        ->name('superadmin.empresas.index');
    
    Route::get('/superadmin/usuarios', [App\Http\Controllers\SuperAdmin\UsuarioViewController::class, 'index'])
        ->name('superadmin.usuarios.index');

    Route::get('/superadmin/motoristas', [App\Http\Controllers\SuperAdmin\EmpresaViewController::class, 'index'])
        ->name('superadmin.motoristas.index');

    Route::get('/superadmin/veiculos', [App\Http\Controllers\SuperAdmin\EmpresaViewController::class, 'index'])
        ->name('superadmin.veiculos.index');

    Route::get('/superadmin/abastecimentos', [App\Http\Controllers\SuperAdmin\EmpresaViewController::class, 'index'])
        ->name('superadmin.abastecimentos.index');
    
    
    Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout.web');
});
