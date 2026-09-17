<?php

namespace App\Repositories;

use App\Contracts\Repositories\HistorialRepositoryInterface;
use App\Models\Historial;

class EloquentHistorialRepository extends EloquentRepository implements HistorialRepositoryInterface
{
    /**
     * Registra una acción en el historial.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Historial
    {
        /** @var Historial */
        return $this->createModel($data);
    }

    /**
     * @return class-string<Historial>
     */
    protected function modelClass(): string
    {
        return Historial::class;
    }
}
