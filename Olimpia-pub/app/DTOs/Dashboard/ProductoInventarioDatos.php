<?php

namespace App\DTOs\Dashboard;

use App\Models\Producto;

final readonly class ProductoInventarioDatos
{
    /**
     * Fila del catálogo de inventario.
     */
    public function __construct(
        public int $id,
        public string $nombre,
        public ?string $descripcion,
        public string $categoria,
        public string $precio,
        public InventarioExistenciaDatos $existencia,
    ) {}

    /**
     * Construye el DTO a partir del modelo persistido.
     */
    public static function fromModel(Producto $producto): self
    {
        $categoria = $producto->categoria;

        return new self(
            (int) $producto->id_producto,
            $producto->nombre,
            $producto->descripcion,
            $categoria?->nombre ?? 'Sin categoría',
            (string) $producto->precio,
            InventarioExistenciaDatos::fromModel($producto),
        );
    }

    /**
     * Segunda línea de la columna de detalle.
     */
    public function detalle(): string
    {
        if (filled($this->descripcion)) {
            return $this->descripcion;
        }

        return $this->categoria;
    }

    /**
     * Precio listo para mostrar.
     */
    public function precioFormateado(): string
    {
        return number_format((float) $this->precio, 2, ',', '.');
    }

    /**
     * Etiqueta del estado de stock.
     */
    public function etiquetaEstadoStock(): string
    {
        return $this->existencia->estadoStock->etiqueta();
    }

    /**
     * Indica si el producto está marcado como activo.
     */
    public function estaActivo(): bool
    {
        return $this->existencia->estado === 'activo';
    }
}
