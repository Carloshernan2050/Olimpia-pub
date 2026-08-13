<?php

namespace App\Services;

use App\Contracts\Repositories\RolRepositoryInterface;
use App\Contracts\Repositories\UsuarioRepositoryInterface;
use App\Contracts\Services\AutenticacionServiceInterface;
use App\DTOs\Autenticacion\RegistrarUsuarioDatos;
use App\DTOs\Autenticacion\UsuarioAutenticadoDatos;
use App\Exceptions\Autenticacion\CorreoYaRegistradoException;
use App\Exceptions\Autenticacion\CredencialesInvalidasException;
use App\Exceptions\Autenticacion\RolNoConfiguradoException;
use App\Exceptions\Autenticacion\UsuarioInactivoException;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Hashing\Hasher;

class AutenticacionService implements AutenticacionServiceInterface
{
    private const ROL_REGISTRO = 'cliente';

    private const ESTADO_ACTIVO = 'activo';

    private const HASH_FALSO = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

    public function __construct(
        private readonly UsuarioRepositoryInterface $usuarioRepository,
        private readonly RolRepositoryInterface $rolRepository,
        private readonly StatefulGuard $guard,
        private readonly Hasher $hasher,
    ) {}

    public function registrar(RegistrarUsuarioDatos $datos): UsuarioAutenticadoDatos
    {
        if ($this->usuarioRepository->findByCorreo($datos->correo) !== null) {
            throw new CorreoYaRegistradoException;
        }

        $rol = $this->rolRepository->findByNombre(self::ROL_REGISTRO);

        if ($rol === null) {
            throw new RolNoConfiguradoException(
                'El rol cliente no está configurado. Ejecuta los seeders de roles.'
            );
        }

        $usuario = $this->usuarioRepository->create([
            ...$datos->toPersistence(),
            'estado' => self::ESTADO_ACTIVO,
            'id_rol' => $rol->id_rol,
        ]);

        $this->guard->login($usuario);

        return UsuarioAutenticadoDatos::fromModel($usuario);
    }

    public function iniciarSesion(string $correo, string $contrasena): UsuarioAutenticadoDatos
    {
        $usuario = $this->usuarioRepository->findByCorreo($correo);
        $hash = $usuario?->getAuthPassword() ?? self::HASH_FALSO;

        if (! $this->hasher->check($contrasena, $hash) || $usuario === null) {
            throw new CredencialesInvalidasException;
        }

        if ($usuario->estado !== self::ESTADO_ACTIVO) {
            throw new UsuarioInactivoException;
        }

        $this->guard->login($usuario);

        return UsuarioAutenticadoDatos::fromModel($usuario);
    }

    public function cerrarSesion(): void
    {
        $this->guard->logout();
    }
}
