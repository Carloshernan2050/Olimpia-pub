<?php

namespace App\Contracts\Repositories;

use App\Models\MovimientoInventario;
use Illuminate\Support\Collection;

interface MovimientoInventarioRepositoryInterface
{
    /**
     * Crea un movimiento con los datos recibidos.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): MovimientoInventario;

    /**
     * Actualiza un movimiento existente.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(MovimientoInventario $movimiento, array $data): MovimientoInventario;

    /**
     * Elimina un movimiento.
     */
    public function delete(MovimientoInventario $movimiento): void;

    /**
     * Busca un movimiento por su identificador.
     */
    public function findById(int $id): ?MovimientoInventario;

    /**
     * Movimientos recientes para el listado de gestión.
     *
     * @return Collection<int, MovimientoInventario>
     */
    public function recientes(): Collection;

    /**
     * Movimientos de un producto, del más reciente al más antiguo.
     *
     * @return Collection<int, MovimientoInventario>
     */
    public function porProducto(int $idProducto): Collection;

    /**
     * Elimina todos los movimientos de un producto.
     */
    public function eliminarDeProducto(int $idProducto): void;

    /**
     * Cantidad total de movimientos registrados.
     */
    public function contar(): int;
}
