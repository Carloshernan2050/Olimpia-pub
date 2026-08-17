<?php

namespace App\Http\Controllers;

use App\Contracts\Services\AutenticacionServiceInterface;
use App\DTOs\Autenticacion\UsuarioAutenticadoDatos;
use App\Exceptions\Autenticacion\CorreoYaRegistradoException;
use App\Exceptions\Autenticacion\CredencialesInvalidasException;
use App\Exceptions\Autenticacion\UsuarioInactivoException;
use App\Http\Requests\IniciarSesionRequest;
use App\Http\Requests\RegistrarUsuarioRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AutenticacionController extends Controller
{
    public function __construct(
        private readonly AutenticacionServiceInterface $autenticacionService,
    ) {}

    public function mostrarRegistro(): View
    {
        return view('registro');
    }

    public function mostrarInicioSesion(): View
    {
        return view('inicio-sesion');
    }

    public function registrar(RegistrarUsuarioRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $usuario = $this->autenticacionService->registrar($request->datos());
        } catch (CorreoYaRegistradoException $exception) {
            throw $this->errorDeValidacion($exception->getMessage());
        }

        return $this->responderSesion(
            $request,
            $usuario,
            'Usuario registrado correctamente.',
            'Registro exitoso. Bienvenido.',
            201
        );
    }

    public function iniciarSesion(IniciarSesionRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $usuario = $this->autenticacionService->iniciarSesion(
                $request->validated('correo'),
                $request->validated('contrasena'),
            );
        } catch (CredencialesInvalidasException|UsuarioInactivoException $exception) {
            throw $this->errorDeValidacion($exception->getMessage());
        }

        return $this->responderSesion(
            $request,
            $usuario,
            'Sesión iniciada correctamente.',
            'Sesión iniciada correctamente.',
            200,
            true
        );
    }

    public function cerrarSesion(Request $request): RedirectResponse|JsonResponse
    {
        $this->autenticacionService->cerrarSesion();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'mensaje' => 'Sesión cerrada correctamente.',
            ]);
        }

        return redirect('/')->with('exito', 'Sesión cerrada correctamente.');
    }

    private function responderSesion(
        Request $request,
        UsuarioAutenticadoDatos $usuario,
        string $mensajeJson,
        string $mensajeRedirect,
        int $status = 200,
        bool $intended = false,
    ): RedirectResponse|JsonResponse {
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'mensaje' => $mensajeJson,
                'usuario' => $usuario->toArray(),
            ], $status);
        }

        $redireccion = $intended ? redirect()->intended('/') : redirect('/');

        return $redireccion->with('exito', $mensajeRedirect);
    }

    private function errorDeValidacion(string $mensaje): ValidationException
    {
        return ValidationException::withMessages([
            'correo' => $mensaje,
        ]);
    }
}
