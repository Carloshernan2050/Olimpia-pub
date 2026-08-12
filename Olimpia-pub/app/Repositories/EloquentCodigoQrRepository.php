<?php

namespace App\Repositories;

use App\Contracts\Repositories\CodigoQrRepositoryInterface;
use App\Models\CodigoQr;

class EloquentCodigoQrRepository implements CodigoQrRepositoryInterface
{
    public function __construct(
        private readonly CodigoQr $model
    ) {
    }

    public function create(array $data): CodigoQr
    {
        return $this->model->newQuery()->create($data);
    }

    public function findByNumero(int $numeroQr): ?CodigoQr
    {
        return $this->model->newQuery()
            ->where('numero_qr', $numeroQr)
            ->first();
    }
}
