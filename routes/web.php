<?php

use App\Http\Controllers\ClienteAuthController;
use App\Http\Controllers\TiendaController;
use Illuminate\Support\Facades\Route;

// La tienda se puede visitar sin iniciar sesión.
Route::get('/', [TiendaController::class, 'index'])->name('inicio');

// Acceso de clientes a través de la tabla users de Laravel.
Route::get('/login', [ClienteAuthController::class, 'loginForm'])->name('login');
Route::post('/login', [ClienteAuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.procesar');
Route::get('/registro', [ClienteAuthController::class, 'registerForm'])->name('registro');
Route::post('/registro', [ClienteAuthController::class, 'register'])
    ->middleware('throttle:5,1')
    ->name('registro.procesar');
Route::post('/logout', [ClienteAuthController::class, 'logout'])->name('logout');

// Diseño del login administrativo, aún sin autenticación implementada.
Route::view('/admin/login', 'admin.login')->name('admin.login');
