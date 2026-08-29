<?php

namespace App\DTOs\Dashboard;

final readonly class GuardarPromocionDatos
{
    /**
     * Datos validados para crear o actualizar una promoción.
     */
    public function __construct(
        public string $nombre,
        public ?string $descripcion,
        public string $descuento,
        public PeriodoPromocion $periodo,
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
            (string) $datos['descuento'],
            new PeriodoPromocion(
                (string) $datos['fecha_inicio'],
                (string) $datos['fecha_fin'],
            ),
            ($datos['estado'] ?? 'activa') === 'inactiva' ? 'inactiva' : 'activa',
        );
    }

    /**
     * Atributos para persistir una promoción nueva.
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
     * Atributos para actualizar una promoción existente.
     *
     * @return array<string, mixed>
     */
    public function paraActualizar(): array
    {
        return [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'descuento' => $this->descuento,
            ...$this->periodo->paraPersistir(),
            'estado' => $this->estado,
        ];
    }
}
