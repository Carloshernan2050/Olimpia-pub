<?php

namespace App\DTOs\Autenticacion;

final readonly class RegistrarUsuarioDatos
{
    /**
     * Crea el DTO con los datos validados del formulario de registro.
     */
    public function __construct(
        public string $primerNombre,
        public ?string $segundoNombre,
        public string $primerApellido,
        public ?string $segundoApellido,
        public string $correo,
        public string $contrasena,
    ) {}

    /**
     * Construye el DTO a partir de un arreglo de datos ya validados.
     *
     * @param  array{primer_nombre: string, segundo_nombre?: string|null, primer_apellido: string, segundo_apellido?: string|null, correo: string, contrasena: string}  $datos
     */
    public static function fromValidated(array $datos): self
    {
        return new self(
            primerNombre: $datos['primer_nombre'],
            segundoNombre: self::opcional($datos['segundo_nombre'] ?? null),
            primerApellido: $datos['primer_apellido'],
            segundoApellido: self::opcional($datos['segundo_apellido'] ?? null),
            correo: $datos['correo'],
            contrasena: $datos['contrasena'],
        );
    }

    /**
     * Devuelve los datos listos para persistir en la base de datos.
     *
     * @return array<string, string|null>
     */
    public function toPersistence(): array
    {
        return [
            'primer_nombre' => $this->primerNombre,
            'segundo_nombre' => $this->segundoNombre,
            'primer_apellido' => $this->primerApellido,
            'segundo_apellido' => $this->segundoApellido,
            'correo' => $this->correo,
            'contrasena' => $this->contrasena,
        ];
    }

    /**
     * Normaliza un valor opcional: recorta espacios y convierte vacío en nulo.
     */
    private static function opcional(?string $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $valor = trim($valor);

        return $valor === '' ? null : $valor;
    }
}
