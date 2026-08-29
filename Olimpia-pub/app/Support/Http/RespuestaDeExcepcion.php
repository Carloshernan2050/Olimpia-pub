<?php

namespace App\Support\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

final class RespuestaDeExcepcion
{
    /**
     * JSON para API o aviso flash para la web.
     */
    public static function jsonOAviso(
        Request $request,
        Throwable $exception,
        int $statusJson,
        RedirectResponse $redireccion,
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson()) {
            return response()->json([
                'mensaje' => $exception->getMessage(),
            ], $statusJson);
        }

        return $redireccion->with('error', $exception->getMessage());
    }
}
