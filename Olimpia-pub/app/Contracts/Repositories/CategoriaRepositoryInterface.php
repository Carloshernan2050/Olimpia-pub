<?php

namespace App\Contracts\Repositories;

use App\Models\Categoria;

interface CategoriaRepositoryInterface
{
    /**
     * Crea una categoría con los datos recibidos.
     */
    public function create(array $data): Categoria;

    /**
     * Busca una categoría por su nombre.
     */
    public function findByNombre(string $nombre): ?Categoria;
}
