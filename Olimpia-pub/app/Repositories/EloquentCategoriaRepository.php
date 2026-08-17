<?php

namespace App\Repositories;

use App\Contracts\Repositories\CategoriaRepositoryInterface;
use App\Models\Categoria;

class EloquentCategoriaRepository extends EloquentRepository implements CategoriaRepositoryInterface
{
    /**
     * Crea una categoría con los datos recibidos.
     */
    public function create(array $data): Categoria
    {
        /** @var Categoria */
        return $this->createModel($data);
    }

    /**
     * Busca una categoría por su nombre.
     */
    public function findByNombre(string $nombre): ?Categoria
    {
        /** @var Categoria|null */
        return $this->findFirstBy('nombre', $nombre);
    }

    /**
     * @return class-string<Categoria>
     */
    protected function modelClass(): string
    {
        return Categoria::class;
    }
}
