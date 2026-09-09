<?php

namespace App\DTOs\Dashboard;

use App\Enums\EstadoStockInventario;
use App\Models\Producto;

final readonly class InventarioExistenciaDatos
{
    /**
     * Stock y estados de un producto en inventario.
     */
    public function __construct(
        public int $stock,
        public string $estado,
        public EstadoStockInventario $estadoStock,
    ) {}

    /**
     * Construye la existencia a partir del modelo persistido.
     */
    public static function fromModel(Producto $producto): self
    {
        $stock = (int) $producto->stock;

        return new self(
            $stock,
            (string) $producto->estado,
            EstadoStockInventario::fromStock($stock),
        );
    }
}
