<?php

namespace App\DTOs\Dashboard;

use App\Models\Producto;

final readonly class ProductoInventarioOpcionDatos
{
    /**
     * Producto para el select del movimiento.
     */
    public function __construct(
        public int $id,
        public string $nombre,
    ) {}

    /**
     * Construye el DTO a partir del modelo persistido.
     */
    public static function fromModel(Producto $producto): self
    {
        return new self(
            (int) $producto->id_producto,
            $producto->nombre,
        );
    }
}
