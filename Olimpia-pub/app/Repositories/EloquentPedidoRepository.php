<?php

namespace App\Repositories;

use App\Contracts\Repositories\PedidoRepositoryInterface;
use App\Enums\EstadoPedido;
use App\Models\Pedido;

class EloquentPedidoRepository extends EloquentRepository implements PedidoRepositoryInterface
{
    /**
     * Crea un pedido con los datos recibidos.
     */
    public function create(array $data): Pedido
    {
        /** @var Pedido */
        return $this->createModel($data);
    }

    /**
     * Actualiza un pedido existente.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Pedido $pedido, array $data): Pedido
    {
        $pedido->update($data);

        return $pedido->fresh() ?? $pedido;
    }

    /**
     * Pedido activo de la mesa, si lo hay.
     */
    public function activoDeMesa(int $idMesa): ?Pedido
    {
        /** @var Pedido|null */
        return $this->newQuery()
            ->where('id_mesa', $idMesa)
            ->where('estado', EstadoPedido::Activo->value)
            ->first();
    }

    /**
     * Busca un pedido con sus productos.
     */
    public function findById(int $id): ?Pedido
    {
        /** @var Pedido|null */
        return $this->newQuery()
            ->with('detalles.producto')
            ->whereKey($id)
            ->first();
    }

    /**
     * Indica si la mesa tiene pedidos asociados.
     */
    public function existenDeMesa(int $idMesa): bool
    {
        return $this->newQuery()->where('id_mesa', $idMesa)->exists();
    }

    /**
     * @return class-string<Pedido>
     */
    protected function modelClass(): string
    {
        return Pedido::class;
    }
}
