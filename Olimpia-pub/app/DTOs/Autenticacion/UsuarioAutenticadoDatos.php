<?php

namespace App\DTOs\Autenticacion;

use App\Exceptions\Autenticacion\RolNoConfiguradoException;
use App\Models\Usuario;

final readonly class UsuarioAutenticadoDatos
{
    /**
     * Crea el DTO con los datos públicos del usuario autenticado.
     */
    public function __construct(
        public int $idUsuario,
        public NombrePersona $nombre,
        public string $correo,
        public string $estado,
        public string $rol,
    ) {}

    /**
     * Construye el DTO a partir del modelo de usuario y su rol.
     */
    public static function fromModel(Usuario $usuario): self
    {
        $usuario->loadMissing('rol');
        $rol = $usuario->rol;

        if ($rol === null) {
            throw new RolNoConfiguradoException(
                'El usuario no tiene un rol asignado.'
            );
        }

        return new self(
            (int) $usuario->id_usuario,
            new NombrePersona(
                (string) $usuario->primer_nombre,
                $usuario->segundo_nombre,
                (string) $usuario->primer_apellido,
                $usuario->segundo_apellido
            ),
            (string) $usuario->correo,
            (string) $usuario->estado,
            (string) $rol->nombre_rol
        );
    }

    /**
     * Convierte el DTO a un arreglo listo para respuestas JSON.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_usuario' => $this->idUsuario,
            ...$this->nombre->toArray(),
            'correo' => $this->correo,
            'estado' => $this->estado,
            'rol' => $this->rol,
        ];
    }
}
