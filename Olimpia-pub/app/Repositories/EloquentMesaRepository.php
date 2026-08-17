<?php

namespace App\Repositories;

use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Models\Mesa;

class EloquentMesaRepository extends EloquentRepository implements MesaRepositoryInterface
{
    /**
     * Crea una mesa con los datos recibidos.
     */
    public function create(array $data): Mesa
    {
        /** @var Mesa */
        return $this->createModel($data);
    }

    /**
     * Busca una mesa por su número.
     */
    public function findByNumero(int $numeroMesa): ?Mesa
    {
        /** @var Mesa|null */
        return $this->findFirstBy('numero_mesa', $numeroMesa);
    }

    /**
     * @return class-string<Mesa>
     */
    protected function modelClass(): string
    {
        return Mesa::class;
    }
}
