<?php

use App\Http\Controllers\AutenticacionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('inicio');

Route::middleware('guest')->group(function () {
    Route::get('/registro', [AutenticacionController::class, 'mostrarRegistro'])
        ->name('registro');
    Route::post('/registro', [AutenticacionController::class, 'registrar']);

    Route::get('/iniciar-sesion', [AutenticacionController::class, 'mostrarInicioSesion'])
        ->name('iniciar-sesion');
    Route::post('/iniciar-sesion', [AutenticacionController::class, 'iniciarSesion']);

    Route::get('/login', [AutenticacionController::class, 'mostrarInicioSesion'])
        ->name('login');
    Route::get('/register', [AutenticacionController::class, 'mostrarRegistro'])
        ->name('register');
});

Route::middleware('auth')->group(function () {
    Route::post('/cerrar-sesion', [AutenticacionController::class, 'cerrarSesion'])
        ->name('cerrar-sesion');
});
