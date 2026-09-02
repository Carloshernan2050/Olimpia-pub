<?php

namespace App\Http\Middleware;

use App\Contracts\Services\AutorizacionInventarioServiceInterface;
use App\Exceptions\Inventario\AccesoInventarioDenegadoException;
use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarAccesoInventario
{
    /**
     * Inyecta la autorización de inventario.
     */
    public function __construct(
        private readonly AutorizacionInventarioServiceInterface $autorizacionInventario,
    ) {}

    /**
     * Bloquea el inventario si el usuario no es empleado o administrador.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if (! $usuario instanceof Usuario || ! $this->autorizacionInventario->puedeAcceder($usuario)) {
            throw new AccesoInventarioDenegadoException;
        }

        return $next($request);
    }
}
