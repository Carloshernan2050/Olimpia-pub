<?php

namespace App\DTOs\Autenticacion;

final readonly class RegistrarUsuarioDatos
{
    public function __construct(
        public string $primerNombre,
        public ?string $segundoNombre,
        public string $primerApellido,
        public ?string $segundoApellido,
        public string $correo,
        public string $contrasena,
    ) {}

    /**
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

    private static function opcional(?string $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $valor = trim($valor);

        return $valor === '' ? null : $valor;
    }
}
