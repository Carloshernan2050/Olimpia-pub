<?php

use App\Exceptions\Autenticacion\RolNoConfiguradoException;
use App\Exceptions\Evento\EventoNoEncontradoException;
use App\Exceptions\Inventario\AccesoInventarioDenegadoException;
use App\Exceptions\Inventario\MovimientoInventarioNoEncontradoException;
use App\Exceptions\Inventario\ProductoConPedidosException;
use App\Exceptions\Inventario\ProductoInventarioNoEncontradoException;
use App\Exceptions\Inventario\ProductoNombreDuplicadoException;
use App\Exceptions\Inventario\StockInsuficienteException;
use App\Exceptions\Mesa\GrupoConVariosPedidosException;
use App\Exceptions\Mesa\GrupoInsuficienteException;
use App\Exceptions\Mesa\GrupoNoEncontradoException;
use App\Exceptions\Mesa\MesaConPedidosException;
use App\Exceptions\Mesa\MesaEnGrupoException;
use App\Exceptions\Mesa\MesaNoEncontradaException;
use App\Exceptions\Mesa\MesaNoUnibleException;
use App\Exceptions\Mesa\MesaNumeroDuplicadoException;
use App\Exceptions\Mesa\MesaYaAgrupadaException;
use App\Exceptions\Mesa\PedidoActivoNoEncontradoException;
use App\Exceptions\Mesa\PedidoActivoYaExisteException;
use App\Exceptions\Promocion\PromocionNoEncontradaException;
use App\Http\Middleware\VerificarAccesoInventario;
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

        $exceptions->render(function (EventoNoEncontradoException $exception, Request $request) {
            return RespuestaDeExcepcion::jsonOAviso(
                $request,
                $exception,
                404,
                redirect()->route('eventos'),
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
            if ($request->routeIs('mesa.menu', 'mesa.pedir')) {
                return RespuestaDeExcepcion::jsonOAviso(
                    $request,
                    $exception,
                    404,
                    redirect()->route('mesa.menu', ['codigo' => $request->route('codigo')]),
                );
            }

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
        $exceptions->render(function (
            MesaNoEncontradaException|GrupoNoEncontradoException|PedidoActivoYaExisteException|PedidoActivoNoEncontradoException|MesaNumeroDuplicadoException|MesaConPedidosException|MesaEnGrupoException|GrupoInsuficienteException|MesaNoUnibleException|MesaYaAgrupadaException|GrupoConVariosPedidosException $exception,
            Request $request,
        ) {
            $codigo = $exception instanceof MesaNoEncontradaException
                || $exception instanceof GrupoNoEncontradoException
                ? 404
                : 422;

            if ($request->routeIs('mesa.menu', 'mesa.pedir')) {
                if ($exception instanceof MesaNoEncontradaException && $request->isMethod('GET')) {
                    return response()->view('pedido.no-encontrada', [
                        'mensaje' => $exception->getMessage(),
                    ], 404);
                }

                return RespuestaDeExcepcion::jsonOAviso(
                    $request,
                    $exception,
                    $codigo,
                    redirect()->route('mesa.menu', ['codigo' => $request->route('codigo')]),
                );
            }

            return RespuestaDeExcepcion::jsonOAviso(
                $request,
                $exception,
                $codigo,
                redirect()->route('mesas'),
            );
        });
    })->create();
