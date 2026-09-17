<?php

namespace App\Contracts\Repositories;

use App\Models\Pedido;

interface PedidoRepositoryInterface
{
    /**
     * Crea un pedido con los datos recibidos.
     */
    public function create(array $data): Pedido;

    /**
     * Actualiza un pedido existente.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Pedido $pedido, array $data): Pedido;

    /**
     * Pedido activo de la mesa, si lo hay.
     */
    public function activoDeMesa(int $idMesa): ?Pedido;

    /**
     * Busca un pedido con sus productos.
     */
    public function findById(int $id): ?Pedido;

    /**
     * Indica si la mesa tiene pedidos asociados.
     */
    public function existenDeMesa(int $idMesa): bool;
}
