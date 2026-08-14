<?php

namespace App\Contracts\Repositories;

use App\Models\Producto;

interface ProductoRepositoryInterface
{
    /**
     * Crea un producto con los datos recibidos.
     */
    public function create(array $data): Producto;

    /**
     * Busca un producto por su nombre.
     */
    public function findByNombre(string $nombre): ?Producto;
}
