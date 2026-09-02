<?php

namespace App\Repositories;

use App\Contracts\Repositories\MovimientoInventarioRepositoryInterface;
use App\Models\MovimientoInventario;
use Illuminate\Support\Collection;

class EloquentMovimientoInventarioRepository extends EloquentRepository implements MovimientoInventarioRepositoryInterface
{
    /**
     * Crea un movimiento con los datos recibidos.
     */
    public function create(array $data): MovimientoInventario
    {
        /** @var MovimientoInventario */
        return $this->createModel($data);
    }

    /**
     * Actualiza un movimiento existente.
     */
    public function update(MovimientoInventario $movimiento, array $data): MovimientoInventario
    {
        $movimiento->update($data);

        return $movimiento->fresh(['producto']) ?? $movimiento;
    }

    /**
     * Elimina un movimiento.
     */
    public function delete(MovimientoInventario $movimiento): void
    {
        $movimiento->delete();
    }

    /**
     * Busca un movimiento por su identificador.
     */
    public function findById(int $id): ?MovimientoInventario
    {
        /** @var MovimientoInventario|null */
        return $this->newQuery()
            ->with('producto')
            ->where('id_movimiento', $id)
            ->first();
    }

    /**
     * Movimientos recientes para el listado de gestión.
     *
     * @return Collection<int, MovimientoInventario>
     */
    public function recientes(): Collection
    {
        return $this->newQuery()
            ->with('producto')
            ->orderByDesc('fecha')
            ->orderByDesc('id_movimiento')
            ->limit(20)
            ->get();
    }

    /**
     * Movimientos de un producto, del más reciente al más antiguo.
     *
     * @return Collection<int, MovimientoInventario>
     */
    public function porProducto(int $idProducto): Collection
    {
        return $this->newQuery()
            ->with('producto')
            ->where('id_producto', $idProducto)
            ->orderByDesc('fecha')
            ->orderByDesc('id_movimiento')
            ->get();
    }

    /**
     * Elimina todos los movimientos de un producto.
     */
    public function eliminarDeProducto(int $idProducto): void
    {
        $this->newQuery()
            ->where('id_producto', $idProducto)
            ->delete();
    }

    /**
     * Cantidad total de movimientos registrados.
     */
    public function contar(): int
    {
        return $this->newQuery()->count();
    }

    /**
     * @return class-string<MovimientoInventario>
     */
    protected function modelClass(): string
    {
        return MovimientoInventario::class;
    }
}
