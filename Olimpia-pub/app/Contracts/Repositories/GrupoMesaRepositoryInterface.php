<?php

namespace App\Contracts\Repositories;

use App\Models\GrupoMesa;
use Illuminate\Support\Collection;

interface GrupoMesaRepositoryInterface
{
    /**
     * Crea un grupo de mesas.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): GrupoMesa;

    /**
     * Busca un grupo con sus mesas, QR y pedido activo.
     */
    public function findById(int $id): ?GrupoMesa;

    /**
     * Grupos del catálogo, con mesas ordenadas por número.
     *
     * @return Collection<int, GrupoMesa>
     */
    public function catalogo(): Collection;

    /**
     * Elimina el grupo; las mesas quedan sueltas.
     */
    public function delete(GrupoMesa $grupo): void;
}
