<?php

use App\Exceptions\Autenticacion\RolNoConfiguradoException;
use App\Exceptions\Inventario\AccesoInventarioDenegadoException;
use App\Exceptions\Inventario\MovimientoInventarioNoEncontradoException;
use App\Exceptions\Inventario\ProductoConPedidosException;
use App\Exceptions\Inventario\ProductoInventarioNoEncontradoException;
use App\Exceptions\Inventario\ProductoNombreDuplicadoException;
use App\Exceptions\Inventario\StockInsuficienteException;
use App\Http\Middleware\VerificarAccesoInventario;
use App\Exceptions\Promocion\PromocionNoEncontradaException;
use App\Support\Http\RespuestaDeExcepcion;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    /**
     * Redirige a invitados al inicio de sesión y a usuarios autenticados al dashboard.
     */
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('iniciar-sesion'));
        $middleware->redirectUsersTo(fn () => route('dashboard'));
        $middleware->alias([
            'acceso-inventario' => VerificarAccesoInventario::class,
        ]);
    })
    /**
     * Responde en JSON para API y muestra el error de rol no configurado.
     */
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (RolNoConfiguradoException $exception, Request $request) {
            return RespuestaDeExcepcion::jsonOAviso($request, $exception, 503, back());
        });

        $exceptions->render(function (PromocionNoEncontradaException $exception, Request $request) {
            return RespuestaDeExcepcion::jsonOAviso(
                $request,
                $exception,
                404,
                redirect()->route('promociones'),
            );
        });

        $exceptions->render(function (AccesoInventarioDenegadoException $exception, Request $request) {
            return RespuestaDeExcepcion::jsonOAviso(
                $request,
                $exception,
                403,
                redirect()->route('dashboard'),
            );
        });

        $exceptions->render(function (
            ProductoInventarioNoEncontradoException|MovimientoInventarioNoEncontradoException $exception,
            Request $request,
        ) {
            return RespuestaDeExcepcion::jsonOAviso(
                $request,
                $exception,
                404,
                redirect()->route('inventario'),
            );
        });

        $exceptions->render(function (
            StockInsuficienteException|ProductoConPedidosException|ProductoNombreDuplicadoException $exception,
            Request $request,
        ) {
            return RespuestaDeExcepcion::jsonOAviso(
                $request,
                $exception,
                422,
                redirect()->route('inventario'),
            );
        });
    })->create();
