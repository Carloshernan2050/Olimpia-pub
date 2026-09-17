<?php

namespace App\Contracts\Repositories;

use App\Models\CodigoQr;

interface CodigoQrRepositoryInterface
{
    /**
     * Crea un código QR con los datos recibidos.
     */
    public function create(array $data): CodigoQr;

    /**
     * Busca un código QR por su número.
     */
    public function findByNumero(int $numeroQr): ?CodigoQr;

    /**
     * Actualiza un código QR existente.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(CodigoQr $codigoQr, array $data): CodigoQr;

    /**
     * Elimina un código QR.
     */
    public function delete(CodigoQr $codigoQr): void;
}
