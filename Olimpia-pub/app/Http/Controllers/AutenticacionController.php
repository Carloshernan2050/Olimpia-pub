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
        return view('registro.registro');
    }

    public function mostrarInicioSesion(): View
    {
        return view('registro.inicio-sesion');
    }

    public function registrar(RegistrarUsuarioRequest $request): RedirectResponse|JsonResponse
    {
        $usuario = $this->intentarAutenticar(
            fn () => $this->autenticacionService->registrar($request->datos())
        );

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
        $usuario = $this->intentarAutenticar(
            fn () => $this->autenticacionService->iniciarSesion(
                $request->validated('correo'),
                $request->validated('contrasena'),
            )
        );

        $mensaje = 'Sesión iniciada correctamente.';

        return $this->responderSesion(
            $request,
            $usuario,
            $mensaje,
            $mensaje,
            200,
            true
        );
    }

    public function cerrarSesion(Request $request): RedirectResponse|JsonResponse
    {
        $this->autenticacionService->cerrarSesion();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $mensaje = 'Sesión cerrada correctamente.';

        if ($request->expectsJson()) {
            return response()->json([
                'mensaje' => $mensaje,
            ]);
        }

        return redirect('/')->with('exito', $mensaje);
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

        $destino = route('dashboard');
        $redireccion = $intended ? redirect()->intended($destino) : redirect($destino);

        return $redireccion->with('exito', $mensajeRedirect);
    }

    /**
     * Convierte errores de dominio esperados en validación del formulario.
     */
    private function intentarAutenticar(callable $accion): UsuarioAutenticadoDatos
    {
        try {
            return $accion();
        } catch (CorreoYaRegistradoException|CredencialesInvalidasException|UsuarioInactivoException $exception) {
            throw $this->errorDeValidacion($exception->getMessage());
        }
    }

    private function errorDeValidacion(string $mensaje): ValidationException
    {
        return ValidationException::withMessages([
            'correo' => $mensaje,
        ]);
    }
}
