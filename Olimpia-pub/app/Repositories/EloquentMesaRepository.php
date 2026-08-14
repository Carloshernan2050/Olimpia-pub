<?php

namespace App\Repositories;

use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Models\Mesa;

class EloquentMesaRepository implements MesaRepositoryInterface
{
    /**
     * Inyecta el modelo de mesa.
     */
    public function __construct(
        private readonly Mesa $model
    ) {
    }

    /**
     * Crea una mesa con los datos recibidos.
     */
    public function create(array $data): Mesa
    {
        return $this->model->newQuery()->create($data);
    }

    /**
     * Busca una mesa por su número.
     */
    public function findByNumero(int $numeroMesa): ?Mesa
    {
        return $this->model->newQuery()
            ->where('numero_mesa', $numeroMesa)
            ->first();
    }
}
