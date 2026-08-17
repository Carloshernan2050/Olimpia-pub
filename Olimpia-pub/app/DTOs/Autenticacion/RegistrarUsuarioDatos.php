<?php

namespace App\DTOs\Autenticacion;

final readonly class RegistrarUsuarioDatos
{
    /**
     * Crea el DTO con los datos validados del formulario de registro.
     */
    public function __construct(
        public NombrePersona $nombre,
        public string $correo,
        public string $contrasena,
    ) {}

    /**
     * Construye el DTO a partir de un arreglo de datos ya validados.
     *
     * @param  array{
     *     primer_nombre: string,
     *     segundo_nombre?: string|null,
     *     primer_apellido: string,
     *     segundo_apellido?: string|null,
     *     correo: string,
     *     contrasena: string
     * }  $datos
     */
    public static function fromValidated(array $datos): self
    {
        return new self(
            new NombrePersona(
                $datos['primer_nombre'],
                self::opcional($datos['segundo_nombre'] ?? null),
                $datos['primer_apellido'],
                self::opcional($datos['segundo_apellido'] ?? null)
            ),
            $datos['correo'],
            $datos['contrasena']
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
            ...$this->nombre->toArray(),
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
