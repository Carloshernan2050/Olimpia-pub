<?php

namespace App\DTOs\Dashboard;

use App\Enums\EstadoEvento;

final readonly class GuardarEventoDatos
{
    /**
     * Datos validados para crear o actualizar un evento.
     */
    public function __construct(
        public string $nombre,
        public ?string $descripcion,
        public string $fecha,
        public string $hora,
        public string $estado,
    ) {}

    /**
     * @param  array<string, mixed>  $datos
     */
    public static function fromValidated(array $datos): self
    {
        $descripcion = trim((string) ($datos['descripcion'] ?? ''));

        return new self(
            trim((string) $datos['nombre']),
            $descripcion === '' ? null : $descripcion,
            (string) $datos['fecha'],
            self::hora((string) $datos['hora']),
            EstadoEvento::desdeValor($datos['estado'] ?? null)->value,
        );
    }

    /**
     * Atributos para persistir un evento nuevo.
     *
     * @return array<string, mixed>
     */
    public function paraCrear(int $idUsuario): array
    {
        return [
            ...$this->paraActualizar(),
            'id_usuario' => $idUsuario,
        ];
    }

    /**
     * Atributos para actualizar un evento existente.
     *
     * @return array<string, mixed>
     */
    public function paraActualizar(): array
    {
        return [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'fecha' => $this->fecha,
            'hora' => $this->hora,
            'estado' => $this->estado,
        ];
    }

    /**
     * Deja la hora en HH:MM.
     */
    private static function hora(string $hora): string
    {
        return substr($hora, 0, 5);
    }
}
