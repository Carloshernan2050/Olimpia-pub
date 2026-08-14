<?php

namespace App\Repositories;

use App\Contracts\Repositories\CodigoQrRepositoryInterface;
use App\Models\CodigoQr;

class EloquentCodigoQrRepository implements CodigoQrRepositoryInterface
{
    /**
     * Inyecta el modelo de código QR.
     */
    public function __construct(
        private readonly CodigoQr $model
    ) {
    }

    /**
     * Crea un código QR con los datos recibidos.
     */
    public function create(array $data): CodigoQr
    {
        return $this->model->newQuery()->create($data);
    }

    /**
     * Busca un código QR por su número.
     */
    public function findByNumero(int $numeroQr): ?CodigoQr
    {
        return $this->model->newQuery()
            ->where('numero_qr', $numeroQr)
            ->first();
    }
}
