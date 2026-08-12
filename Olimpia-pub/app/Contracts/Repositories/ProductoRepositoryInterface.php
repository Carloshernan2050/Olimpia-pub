<?php

namespace App\Contracts\Repositories;

use App\Models\Producto;

interface ProductoRepositoryInterface
{
    public function create(array $data): Producto;

    public function findByNombre(string $nombre): ?Producto;
}
