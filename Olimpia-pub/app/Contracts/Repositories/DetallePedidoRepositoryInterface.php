<?php

namespace App\Contracts\Repositories;

use App\Models\DetallePedido;

interface DetallePedidoRepositoryInterface
{
    /**
     * Crea un renglón de pedido.
     */
    public function create(array $data): DetallePedido;
}
