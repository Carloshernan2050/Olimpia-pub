<?php

namespace App\DTOs\Autenticacion;

use App\Models\Usuario;

final readonly class UsuarioAutenticadoDatos
{
    /**
     * Crea el DTO con los datos públicos del usuario autenticado.
     */
    public function __construct(
        public int $idUsuario,
        public string $primerNombre,
        public ?string $segundoNombre,
        public string $primerApellido,
        public ?string $segundoApellido,
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

        return new self(
            idUsuario: (int) $usuario->id_usuario,
            primerNombre: (string) $usuario->primer_nombre,
            segundoNombre: $usuario->segundo_nombre,
            primerApellido: (string) $usuario->primer_apellido,
            segundoApellido: $usuario->segundo_apellido,
            correo: (string) $usuario->correo,
            estado: (string) $usuario->estado,
            rol: (string) $usuario->rol->nombre_rol,
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
            'primer_nombre' => $this->primerNombre,
            'segundo_nombre' => $this->segundoNombre,
            'primer_apellido' => $this->primerApellido,
            'segundo_apellido' => $this->segundoApellido,
            'correo' => $this->correo,
            'estado' => $this->estado,
            'rol' => $this->rol,
        ];
    }
}
