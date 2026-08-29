<?php

namespace App\Contracts\Repositories;

use App\DTOs\Dashboard\FiltroPromocionesDatos;
use App\Models\Promocion;
use Illuminate\Support\Collection;

interface PromocionRepositoryInterface
{
    /**
     * Crea una promoción con los datos recibidos.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Promocion;

    /**
     * Actualiza una promoción existente.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Promocion $promocion, array $data): Promocion;

    /**
     * Elimina una promoción y sus productos asociados.
     */
    public function delete(Promocion $promocion): void;

    /**
     * Busca una promoción por su identificador.
     */
    public function findById(int $id): ?Promocion;

    /**
     * Promociones activas filtradas y ordenadas para el catálogo.
     *
     * @return Collection<int, Promocion>
     */
    public function activas(?FiltroPromocionesDatos $filtro = null): Collection;

    /**
     * Todas las promociones para el listado de gestión.
     *
     * @return Collection<int, Promocion>
     */
    public function todas(): Collection;
}
