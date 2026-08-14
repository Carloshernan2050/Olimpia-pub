<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProductoRepositoryInterface;
use App\Models\Producto;

class EloquentProductoRepository implements ProductoRepositoryInterface
{
    /**
     * Inyecta el modelo de producto.
     */
    public function __construct(
        private readonly Producto $model
    ) {
    }

    /**
     * Crea un producto con los datos recibidos.
     */
    public function create(array $data): Producto
    {
        return $this->model->newQuery()->create($data);
    }

    /**
     * Busca un producto por su nombre.
     */
    public function findByNombre(string $nombre): ?Producto
    {
        return $this->model->newQuery()
            ->where('nombre', $nombre)
            ->first();
    }
}
