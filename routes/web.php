<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;


//Route::get('/criar-conta', [UsuarioController::class, 'create'])->name('create-account');
//Route::post('/criar-conta', [UsuarioController::class, 'store'])->name('insert-account');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'loginWeb'])->name('login.web');

Route::middleware(['auth'])->group(function() {

    Route::get('/dashboard-admin', function () {
        return view('dashboard_admin');
    })->name('dashboard.admin');

    Route::get('/dashboard-superadmin', function () {
        return view('dashboard_superadmin');
    })->name('dashboard.superadmin');

    Route::get('/superadmin/empresas', [App\Http\Controllers\SuperAdmin\EmpresaViewController::class, 'index'])
        ->name('superadmin.empresas.index');

    Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout.web');
});
