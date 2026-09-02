<?php

namespace App\Repositories;

use App\Contracts\Repositories\CategoriaRepositoryInterface;
use App\Models\Categoria;
use Illuminate\Support\Collection;

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
     * Todas las categorías ordenadas por nombre.
     *
     * @return Collection<int, Categoria>
     */
    public function todas(): Collection
    {
        return $this->newQuery()
            ->orderBy('nombre')
            ->get();
    }

    /**
     * @return class-string<Categoria>
     */
    protected function modelClass(): string
    {
        return Categoria::class;
    }
}
