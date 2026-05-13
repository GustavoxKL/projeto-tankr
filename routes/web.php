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

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout.web');
});
