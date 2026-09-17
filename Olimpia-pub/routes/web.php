<?php

use App\Http\Controllers\AutenticacionController;
use App\Http\Controllers\MesaPedidoController;
use Illuminate\Support\Facades\Route;

/**
 * Muestra la página de inicio.
 */
Route::get('/', function () {
    return view('registro.bienvenida');
})->middleware('guest')->name('inicio');

/**
 * Rutas disponibles solo para visitantes.
 */
Route::middleware('guest')->group(function () {
    Route::get('/registro', [AutenticacionController::class, 'mostrarRegistro'])
        ->name('registro');
    Route::post('/registro', [AutenticacionController::class, 'registrar'])
        ->middleware('throttle:registro')
        ->name('registro.guardar');

    Route::get('/iniciar-sesion', [AutenticacionController::class, 'mostrarInicioSesion'])
        ->name('iniciar-sesion');
    Route::post('/iniciar-sesion', [AutenticacionController::class, 'iniciarSesion'])
        ->middleware('throttle:inicio-sesion')
        ->name('iniciar-sesion.guardar');

    Route::get('/login', [AutenticacionController::class, 'mostrarInicioSesion'])
        ->name('login');
    Route::get('/register', [AutenticacionController::class, 'mostrarRegistro'])
        ->name('register');
});

/**
 * Rutas disponibles solo para usuarios autenticados.
 */
Route::middleware('auth')->group(function () {
    Route::post('/cerrar-sesion', [AutenticacionController::class, 'cerrarSesion'])
        ->name('cerrar-sesion');
});

/**
 * Carta pública de la mesa, abierta al escanear el QR.
 */
Route::get('/mesa/{codigo}', [MesaPedidoController::class, 'mostrar'])
    ->where('codigo', '[A-Za-z0-9\-]+')
    ->name('mesa.menu');
Route::post('/mesa/{codigo}/pedido', [MesaPedidoController::class, 'pedir'])
    ->where('codigo', '[A-Za-z0-9\-]+')
    ->middleware('throttle:pedidos-mesa')
    ->name('mesa.pedir');

require __DIR__.'/dashboard.php'; // NOSONAR Laravel recarga web.php y require_once omitiría las rutas.
