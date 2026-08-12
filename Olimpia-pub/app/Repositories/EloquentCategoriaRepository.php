<?php

namespace App\Repositories;

use App\Contracts\Repositories\CategoriaRepositoryInterface;
use App\Models\Categoria;

class EloquentCategoriaRepository implements CategoriaRepositoryInterface
{
    public function __construct(
        private readonly Categoria $model
    ) {
    }

    public function create(array $data): Categoria
    {
        return $this->model->newQuery()->create($data);
    }

    public function findByNombre(string $nombre): ?Categoria
    {
        return $this->model->newQuery()
            ->where('nombre', $nombre)
            ->first();
    }
}
