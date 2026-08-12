<?php

namespace App\Http\Controllers;

use App\Contracts\Services\AutenticacionServiceInterface;
use App\Exceptions\Autenticacion\CorreoYaRegistradoException;
use App\Exceptions\Autenticacion\CredencialesInvalidasException;
use App\Exceptions\Autenticacion\RolNoConfiguradoException;
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
    ) {
    }

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
            $usuario = $this->autenticacionService->registrar($request->safe()->only([
                'nombre',
                'apellido',
                'correo',
                'contrasena',
            ]));
        } catch (CorreoYaRegistradoException $exception) {
            throw ValidationException::withMessages([
                'correo' => $exception->getMessage(),
            ]);
        } catch (RolNoConfiguradoException $exception) {
            throw ValidationException::withMessages([
                'correo' => $exception->getMessage(),
            ]);
        }

        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'mensaje' => 'Usuario registrado correctamente.',
                'usuario' => $usuario,
            ], 201);
        }

        return redirect('/')->with('exito', 'Registro exitoso. Bienvenido.');
    }

    public function iniciarSesion(IniciarSesionRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $usuario = $this->autenticacionService->iniciarSesion(
                $request->validated('correo'),
                $request->validated('contrasena'),
            );
        } catch (CredencialesInvalidasException $exception) {
            throw ValidationException::withMessages([
                'correo' => $exception->getMessage(),
            ]);
        } catch (UsuarioInactivoException $exception) {
            throw ValidationException::withMessages([
                'correo' => $exception->getMessage(),
            ]);
        }

        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'mensaje' => 'Sesión iniciada correctamente.',
                'usuario' => $usuario,
            ]);
        }

        return redirect()->intended('/')->with('exito', 'Sesión iniciada correctamente.');
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
}
