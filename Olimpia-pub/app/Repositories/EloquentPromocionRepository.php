<?php

namespace App\Repositories;

use App\Contracts\Repositories\PromocionRepositoryInterface;
use App\DTOs\Dashboard\FiltroPromocionesDatos;
use App\Models\Promocion;
use Illuminate\Support\Collection;

class EloquentPromocionRepository extends EloquentRepository implements PromocionRepositoryInterface
{
    /**
     * Crea una promoción con los datos recibidos.
     */
    public function create(array $data): Promocion
    {
        /** @var Promocion */
        return $this->createModel($data);
    }

    /**
     * Actualiza una promoción existente.
     */
    public function update(Promocion $promocion, array $data): Promocion
    {
        $promocion->update($data);

        return $promocion->fresh() ?? $promocion;
    }

    /**
     * Elimina una promoción y desvincula sus productos.
     */
    public function delete(Promocion $promocion): void
    {
        $promocion->productos()->detach();
        $promocion->delete();
    }

    /**
     * Busca una promoción por su identificador.
     */
    public function findById(int $id): ?Promocion
    {
        /** @var Promocion|null */
        return $this->findFirstBy('id_promocion', $id);
    }

    /**
     * Promociones activas cuyo periodo solapa el rango (hoy si no hay fechas).
     *
     * @return Collection<int, Promocion>
     */
    public function activas(?FiltroPromocionesDatos $filtro = null): Collection
    {
        $filtro ??= FiltroPromocionesDatos::predeterminado();
        $consulta = $this->newQuery()->where('estado', 'activa');

        if ($filtro->desde === null && $filtro->hasta === null) {
            $hoy = now()->toDateString();
            $consulta->whereDate('fecha_inicio', '<=', $hoy)
                ->whereDate('fecha_fin', '>=', $hoy);
        } else {
            if ($filtro->desde !== null) {
                $consulta->whereDate('fecha_fin', '>=', $filtro->desde);
            }

            if ($filtro->hasta !== null) {
                $consulta->whereDate('fecha_inicio', '<=', $filtro->hasta);
            }
        }

        return $consulta
            ->orderBy('fecha_inicio')
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Todas las promociones ordenadas por nombre.
     *
     * @return Collection<int, Promocion>
     */
    public function todas(): Collection
    {
        return $this->newQuery()
            ->orderBy('nombre')
            ->get();
    }

    /**
     * @return class-string<Promocion>
     */
    protected function modelClass(): string
    {
        return Promocion::class;
    }
}
