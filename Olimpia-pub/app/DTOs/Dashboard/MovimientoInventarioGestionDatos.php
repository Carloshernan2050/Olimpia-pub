<?php

namespace App\DTOs\Dashboard;

use App\Enums\TipoMovimientoInventario;
use App\Models\MovimientoInventario;

final readonly class MovimientoInventarioGestionDatos
{
    /**
     * Datos de un movimiento para el formulario y el listado de gestión.
     */
    public function __construct(
        public int $id,
        public int $idProducto,
        public string $nombreProducto,
        public TipoMovimientoInventario $tipo,
        public int $cantidad,
        public string $fecha,
    ) {}

    /**
     * Construye el DTO a partir del modelo persistido.
     */
    public static function fromModel(MovimientoInventario $movimiento): self
    {
        $tipo = TipoMovimientoInventario::tryFrom((string) $movimiento->tipo_movimiento)
            ?? TipoMovimientoInventario::Entrada;

        return new self(
            (int) $movimiento->id_movimiento,
            (int) $movimiento->id_producto,
            $movimiento->producto?->nombre ?? 'Producto',
            $tipo,
            (int) $movimiento->cantidad,
            $movimiento->fecha?->format('Y-m-d H:i') ?? '',
        );
    }

    /**
     * Etiqueta del tipo de movimiento.
     */
    public function etiquetaTipo(): string
    {
        return $this->tipo->etiqueta();
    }
}
