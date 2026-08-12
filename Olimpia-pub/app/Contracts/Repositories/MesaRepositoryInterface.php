<?php

namespace App\Contracts\Repositories;

use App\Models\Mesa;

interface MesaRepositoryInterface
{
    public function create(array $data): Mesa;

    public function findByNumero(int $numeroMesa): ?Mesa;
}
