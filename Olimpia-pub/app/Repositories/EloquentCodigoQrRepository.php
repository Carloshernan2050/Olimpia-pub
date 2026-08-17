<?php

namespace App\Repositories;

use App\Contracts\Repositories\CodigoQrRepositoryInterface;
use App\Models\CodigoQr;

class EloquentCodigoQrRepository extends EloquentRepository implements CodigoQrRepositoryInterface
{
    /**
     * Crea un código QR con los datos recibidos.
     */
    public function create(array $data): CodigoQr
    {
        /** @var CodigoQr */
        return $this->createModel($data);
    }

    /**
     * Busca un código QR por su número.
     */
    public function findByNumero(int $numeroQr): ?CodigoQr
    {
        /** @var CodigoQr|null */
        return $this->findFirstBy('numero_qr', $numeroQr);
    }

    /**
     * @return class-string<CodigoQr>
     */
    protected function modelClass(): string
    {
        return CodigoQr::class;
    }
}
