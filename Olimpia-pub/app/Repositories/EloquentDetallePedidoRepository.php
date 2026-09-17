<?php

namespace App\Repositories;

use App\Contracts\Repositories\DetallePedidoRepositoryInterface;
use App\Models\DetallePedido;

class EloquentDetallePedidoRepository extends EloquentRepository implements DetallePedidoRepositoryInterface
{
    /**
     * Crea un renglón de pedido.
     */
    public function create(array $data): DetallePedido
    {
        /** @var DetallePedido */
        return $this->createModel($data);
    }

    /**
     * @return class-string<DetallePedido>
     */
    protected function modelClass(): string
    {
        return DetallePedido::class;
    }
}
