<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProductoRepositoryInterface;
use App\Models\Producto;

class EloquentProductoRepository extends EloquentRepository implements ProductoRepositoryInterface
{
    /**
     * Crea un producto con los datos recibidos.
     */
    public function create(array $data): Producto
    {
        /** @var Producto */
        return $this->createModel($data);
    }

    /**
     * Busca un producto por su nombre.
     */
    public function findByNombre(string $nombre): ?Producto
    {
        /** @var Producto|null */
        return $this->findFirstBy('nombre', $nombre);
    }

    /**
     * @return class-string<Producto>
     */
    protected function modelClass(): string
    {
        return Producto::class;
    }
}
