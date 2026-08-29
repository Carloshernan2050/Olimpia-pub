<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\PromocionController;
use Illuminate\Support\Facades\Route;

/**
 * Rutas del dashboard autenticado.
 */
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'mostrar'])
        ->name('dashboard');
    Route::get('/dashboard/promociones', [PromocionController::class, 'mostrar'])
        ->name('promociones');
    Route::post('/dashboard/promociones', [PromocionController::class, 'guardar'])
        ->name('promociones.guardar');
    Route::put('/dashboard/promociones/{promocion}', [PromocionController::class, 'actualizar'])
        ->whereNumber('promocion')
        ->name('promociones.actualizar');
    Route::delete('/dashboard/promociones/{promocion}', [PromocionController::class, 'eliminar'])
        ->whereNumber('promocion')
        ->name('promociones.eliminar');
});
