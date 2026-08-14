<?php

namespace App\Repositories;

use App\Contracts\Repositories\CategoriaRepositoryInterface;
use App\Models\Categoria;

class EloquentCategoriaRepository implements CategoriaRepositoryInterface
{
    /**
     * Inyecta el modelo de categoría.
     */
    public function __construct(
        private readonly Categoria $model
    ) {
    }

    /**
     * Crea una categoría con los datos recibidos.
     */
    public function create(array $data): Categoria
    {
        return $this->model->newQuery()->create($data);
    }

    /**
     * Busca una categoría por su nombre.
     */
    public function findByNombre(string $nombre): ?Categoria
    {
        return $this->model->newQuery()
            ->where('nombre', $nombre)
            ->first();
    }
}
