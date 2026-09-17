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
        return $this->newQuery()
            ->with('mesa')
            ->where('numero_qr', $numeroQr)
            ->first();
    }

    /**
     * Actualiza un código QR existente.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(CodigoQr $codigoQr, array $data): CodigoQr
    {
        $codigoQr->update($data);

        return $codigoQr->fresh() ?? $codigoQr;
    }

    /**
     * Elimina un código QR.
     */
    public function delete(CodigoQr $codigoQr): void
    {
        $codigoQr->delete();
    }

    /**
     * @return class-string<CodigoQr>
     */
    protected function modelClass(): string
    {
        return CodigoQr::class;
    }
}
