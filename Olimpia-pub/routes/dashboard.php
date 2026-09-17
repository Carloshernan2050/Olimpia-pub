<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\EventoController;
use App\Http\Controllers\Dashboard\InventarioController;
use App\Http\Controllers\Dashboard\MenuController;
use App\Http\Controllers\Dashboard\MesaController;
use App\Http\Controllers\Dashboard\PromocionController;
use App\Support\Dashboard\RutasDashboard;
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
    Route::get('/dashboard/menu', [MenuController::class, 'mostrar'])
        ->name('menu');
    Route::get('/dashboard/mesas', [MesaController::class, 'mostrar'])
        ->name('mesas');
    Route::post('/dashboard/mesas', [MesaController::class, 'guardar'])
        ->name('mesas.guardar');
    Route::post('/dashboard/mesas/grupos', [MesaController::class, 'guardarGrupo'])
        ->name('mesas.grupos.guardar');
    Route::post('/dashboard/mesas/grupos/liberar', [MesaController::class, 'liberarGrupos'])
        ->name('mesas.grupos.liberar');
    Route::put(RutasDashboard::grupoMesaPorId(), [MesaController::class, 'actualizarGrupo'])
        ->whereNumber('grupo')
        ->name('mesas.grupos.actualizar');
    Route::delete(RutasDashboard::grupoMesaPorId(), [MesaController::class, 'eliminarGrupo'])
        ->whereNumber('grupo')
        ->name('mesas.grupos.eliminar');
    Route::post(RutasDashboard::mesaPorId().'/pedido/terminar', [MesaController::class, 'terminarPedido'])
        ->whereNumber('mesa')
        ->name('mesas.pedido.terminar');
    Route::post(RutasDashboard::grupoMesaPorId().'/pedido/terminar', [MesaController::class, 'terminarPedidoGrupo'])
        ->whereNumber('grupo')
        ->name('mesas.grupos.pedido.terminar');
    Route::put(RutasDashboard::mesaPorId(), [MesaController::class, 'actualizar'])
        ->whereNumber('mesa')
        ->name('mesas.actualizar');
    Route::delete(RutasDashboard::mesaPorId(), [MesaController::class, 'eliminar'])
        ->whereNumber('mesa')
        ->name('mesas.eliminar');
    Route::get('/dashboard/eventos', [EventoController::class, 'mostrar'])
        ->name('eventos');
    Route::post('/dashboard/eventos', [EventoController::class, 'guardar'])
        ->name('eventos.guardar');
    Route::get(RutasDashboard::eventoPorId(), [EventoController::class, 'detalle'])
        ->whereNumber('evento')
        ->name('eventos.detalle');
    Route::put(RutasDashboard::eventoPorId(), [EventoController::class, 'actualizar'])
        ->whereNumber('evento')
        ->name('eventos.actualizar');
    Route::delete(RutasDashboard::eventoPorId(), [EventoController::class, 'eliminar'])
        ->whereNumber('evento')
        ->name('eventos.eliminar');
    Route::middleware('acceso-inventario')->group(function () {
        Route::get('/dashboard/inventario', [InventarioController::class, 'mostrar'])
            ->name('inventario');
        Route::post('/dashboard/inventario', [InventarioController::class, 'guardar'])
            ->name('inventario.guardar');
        Route::post('/dashboard/inventario/producto', [InventarioController::class, 'guardarProducto'])
            ->name('inventario.producto.guardar');
        Route::put('/dashboard/inventario/producto/{producto}', [InventarioController::class, 'actualizarProducto'])
            ->whereNumber('producto')
            ->name('inventario.producto.actualizar');
        Route::put('/dashboard/inventario/{movimiento}', [InventarioController::class, 'actualizar'])
            ->whereNumber('movimiento')
            ->name('inventario.actualizar');
        Route::delete('/dashboard/inventario/{movimiento}', [InventarioController::class, 'eliminar'])
            ->whereNumber('movimiento')
            ->name('inventario.eliminar');
        Route::delete('/dashboard/inventario/producto/{producto}', [InventarioController::class, 'eliminarProducto'])
            ->whereNumber('producto')
            ->name('inventario.producto.eliminar');
    });
});
