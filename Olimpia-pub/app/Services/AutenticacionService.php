<?php

namespace App\Services;

use App\Contracts\Repositories\RolRepositoryInterface;
use App\Contracts\Repositories\UsuarioRepositoryInterface;
use App\Contracts\Services\AutenticacionServiceInterface;
use App\Exceptions\Autenticacion\CorreoYaRegistradoException;
use App\Exceptions\Autenticacion\CredencialesInvalidasException;
use App\Exceptions\Autenticacion\RolNoConfiguradoException;
use App\Exceptions\Autenticacion\UsuarioInactivoException;
use App\Models\Usuario;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Hashing\Hasher;

class AutenticacionService implements AutenticacionServiceInterface
{
    private const ROL_REGISTRO = 'cliente';

    private const ESTADO_ACTIVO = 'activo';

    public function __construct(
        private readonly UsuarioRepositoryInterface $usuarioRepository,
        private readonly RolRepositoryInterface $rolRepository,
        private readonly StatefulGuard $guard,
        private readonly Hasher $hasher,
    ) {
    }

    public function registrar(array $datos): Usuario
    {
        if ($this->usuarioRepository->findByCorreo($datos['correo']) !== null) {
            throw new CorreoYaRegistradoException;
        }

        $rol = $this->rolRepository->findByNombre(self::ROL_REGISTRO);

        if ($rol === null) {
            throw new RolNoConfiguradoException(
                'El rol cliente no está configurado. Ejecuta los seeders de roles.'
            );
        }

        $usuario = $this->usuarioRepository->create([
            'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'correo' => $datos['correo'],
            'contrasena' => $datos['contrasena'],
            'estado' => self::ESTADO_ACTIVO,
            'id_rol' => $rol->id_rol,
        ]);

        $this->guard->login($usuario);

        return $usuario->load('rol');
    }

    public function iniciarSesion(string $correo, string $contrasena): Usuario
    {
        $usuario = $this->usuarioRepository->findByCorreo($correo);

        if ($usuario === null || ! $this->hasher->check($contrasena, $usuario->getAuthPassword())) {
            throw new CredencialesInvalidasException;
        }

        if ($usuario->estado !== self::ESTADO_ACTIVO) {
            throw new UsuarioInactivoException;
        }

        $this->guard->login($usuario);

        return $usuario->load('rol');
    }

    public function cerrarSesion(): void
    {
        $this->guard->logout();
    }
}
