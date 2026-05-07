<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;


Route::get('/criar-conta', [UsuarioController::class, 'create'])->name('create-account');
Route::post('/criar-conta', [UsuarioController::class, 'store'])->name('insert-account');


Route::get('/', [AuthController::class, 'index'])->name('login');

Route::get('/dashboard', function () {
    return view('dashboard');
});

//Route::post('/login', [AuthController::class, 'login'])->name('auth');

//Route::middleware(['auth'])->group(function() {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
//});

