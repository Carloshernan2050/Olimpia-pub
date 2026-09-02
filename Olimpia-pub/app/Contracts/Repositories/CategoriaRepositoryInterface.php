<?php

namespace App\Contracts\Repositories;

use App\Models\Categoria;
use Illuminate\Support\Collection;

interface CategoriaRepositoryInterface
{
    /**
     * Crea una categoría con los datos recibidos.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Categoria;

    /**
     * Busca una categoría por su nombre.
     */
    public function findByNombre(string $nombre): ?Categoria;

    /**
     * Todas las categorías ordenadas por nombre.
     *
     * @return Collection<int, Categoria>
     */
    public function todas(): Collection;
}
