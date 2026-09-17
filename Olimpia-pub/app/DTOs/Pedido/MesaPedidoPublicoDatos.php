<?php

namespace App\DTOs\Pedido;

use App\DTOs\Dashboard\CatalogoMenuDatos;
use App\DTOs\Dashboard\PedidoMesaDatos;

final readonly class MesaPedidoPublicoDatos
{
    /**
     * Mesa identificada por QR, con el menú listo para pedir.
     */
    public function __construct(
        public int $id,
        public int $numero,
        public string $codigo,
        public CatalogoMenuDatos $catalogo,
        public ?PedidoMesaDatos $pedidoActivo,
    ) {}

    /**
     * Etiqueta visible en la carta pública.
     */
    public function etiqueta(): string
    {
        return 'Mesa '.$this->numero;
    }
}
