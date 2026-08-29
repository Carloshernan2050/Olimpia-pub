<?php

use App\Exceptions\Autenticacion\RolNoConfiguradoException;
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
    })->create();
