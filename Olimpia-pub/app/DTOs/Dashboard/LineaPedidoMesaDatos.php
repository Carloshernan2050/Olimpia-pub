<?php

namespace App\DTOs\Dashboard;

use App\Models\DetallePedido;

final readonly class LineaPedidoMesaDatos
{
    use ConImagenPublica;

    /**
     * Producto de un pedido activo de mesa.
     */
    public function __construct(
        public string $nombre,
        public int $cantidad,
        public string $precio,
        public ?string $urlImagen = null,
    ) {}

    /**
     * Construye la línea a partir del detalle persistido.
     */
    public static function fromModel(DetallePedido $detalle): self
    {
        $producto = $detalle->producto;

        return new self(
            $producto?->nombre ?? 'Producto',
            (int) $detalle->cantidad,
            (string) $detalle->precio_unitario,
            $producto?->url_imagen,
        );
    }

    /**
     * Precio listo para mostrar.
     */
    public function precioFormateado(): string
    {
        return '$ '.number_format((float) $this->precio, 2, ',', '.');
    }
}
