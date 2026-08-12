<?php

namespace App\Repositories;

use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Models\Mesa;

class EloquentMesaRepository implements MesaRepositoryInterface
{
    public function __construct(
        private readonly Mesa $model
    ) {
    }

    public function create(array $data): Mesa
    {
        return $this->model->newQuery()->create($data);
    }

    public function findByNumero(int $numeroMesa): ?Mesa
    {
        return $this->model->newQuery()
            ->where('numero_mesa', $numeroMesa)
            ->first();
    }
}
