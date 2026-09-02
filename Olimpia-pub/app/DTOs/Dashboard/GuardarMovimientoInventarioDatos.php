<?php

namespace App\DTOs\Dashboard;

use App\Enums\TipoMovimientoInventario;

final readonly class GuardarMovimientoInventarioDatos
{
    /**
     * Datos validados para registrar o actualizar un movimiento.
     */
    public function __construct(
        public int $idProducto,
        public TipoMovimientoInventario $tipo,
        public int $cantidad,
    ) {}

    /**
     * @param  array<string, mixed>  $datos
     */
    public static function fromValidated(array $datos): self
    {
        return new self(
            (int) $datos['id_producto'],
            TipoMovimientoInventario::from((string) $datos['tipo_movimiento']),
            (int) $datos['cantidad'],
        );
    }

    /**
     * Atributos para persistir un movimiento nuevo.
     *
     * @return array<string, mixed>
     */
    public function paraCrear(int $idUsuario): array
    {
        return [
            ...$this->paraActualizar(),
            'fecha' => now(),
            'id_usuario' => $idUsuario,
        ];
    }

    /**
     * Atributos para actualizar un movimiento existente.
     *
     * @return array<string, mixed>
     */
    public function paraActualizar(): array
    {
        return [
            'tipo_movimiento' => $this->tipo->value,
            'cantidad' => $this->cantidad,
            'id_producto' => $this->idProducto,
        ];
    }
}
