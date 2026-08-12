<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProductoRepositoryInterface;
use App\Models\Producto;

class EloquentProductoRepository implements ProductoRepositoryInterface
{
    public function __construct(
        private readonly Producto $model
    ) {
    }

    public function create(array $data): Producto
    {
        return $this->model->newQuery()->create($data);
    }

    public function findByNombre(string $nombre): ?Producto
    {
        return $this->model->newQuery()
            ->where('nombre', $nombre)
            ->first();
    }
}
