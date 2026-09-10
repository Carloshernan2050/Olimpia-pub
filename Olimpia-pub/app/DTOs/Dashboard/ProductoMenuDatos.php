<?php

namespace App\DTOs\Dashboard;

use App\Models\Producto;

final readonly class ProductoMenuDatos
{
    /**
     * Tarjeta de consulta del menú, con datos tomados del inventario.
     */
    public function __construct(
        public int $id,
        public string $nombre,
        public ?string $descripcion,
        public string $categoria,
        public string $precio,
    ) {}

    /**
     * Construye el DTO a partir del producto persistido en inventario.
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
        );
    }

    /**
     * Precio listo para mostrar, tomado del inventario.
     */
    public function precioFormateado(): string
    {
        return '$ '.number_format((float) $this->precio, 2, ',', '.');
    }
}
