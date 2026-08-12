<?php

namespace App\Contracts\Repositories;

use App\Models\Categoria;

interface CategoriaRepositoryInterface
{
    public function create(array $data): Categoria;

    public function findByNombre(string $nombre): ?Categoria;
}
