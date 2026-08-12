<?php

namespace App\Contracts\Repositories;

use App\Models\CodigoQr;

interface CodigoQrRepositoryInterface
{
    public function create(array $data): CodigoQr;

    public function findByNumero(int $numeroQr): ?CodigoQr;
}
