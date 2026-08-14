<?php

namespace App\Contracts\Repositories;

use App\Models\Mesa;

interface MesaRepositoryInterface
{
    /**
     * Crea una mesa con los datos recibidos.
     */
    public function create(array $data): Mesa;

    /**
     * Busca una mesa por su número.
     */
    public function findByNumero(int $numeroMesa): ?Mesa;
}
