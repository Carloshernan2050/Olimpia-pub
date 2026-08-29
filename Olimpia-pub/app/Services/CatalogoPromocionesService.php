<?php

namespace App\Services;

use App\Contracts\Repositories\PromocionRepositoryInterface;
use App\Contracts\Services\CatalogoPromocionesServiceInterface;
use App\DTOs\Dashboard\CatalogoPromocionesDatos;
use App\DTOs\Dashboard\FiltroPromocionesDatos;
use App\DTOs\Dashboard\PromocionTarjetaDatos;
use App\Models\Promocion;

class CatalogoPromocionesService implements CatalogoPromocionesServiceInterface
{
    /**
     * Inyecta el repositorio de promociones.
     */
    public function __construct(
        private readonly PromocionRepositoryInterface $promocionRepository,
    ) {}

    /**
     * Convierte las promociones filtradas en tarjetas del catálogo.
     */
    public function obtenerCatalogo(?FiltroPromocionesDatos $filtro = null): CatalogoPromocionesDatos
    {
        $filtro ??= FiltroPromocionesDatos::predeterminado();

        $promociones = $this->promocionRepository
            ->activas($filtro)
            ->map(fn (Promocion $promocion): PromocionTarjetaDatos => PromocionTarjetaDatos::fromModel($promocion))
            ->values()
            ->all();

        return new CatalogoPromocionesDatos($promociones);
    }
}
