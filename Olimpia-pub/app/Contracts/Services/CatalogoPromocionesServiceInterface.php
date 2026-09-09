<?php

namespace App\Contracts\Services;

use App\DTOs\Dashboard\CatalogoPromocionesDatos;
use App\DTOs\Dashboard\FiltroRangoFechasDatos;

interface CatalogoPromocionesServiceInterface
{
    /**
     * Arma el catálogo de promociones vigentes según el filtro.
     */
    public function obtenerCatalogo(?FiltroRangoFechasDatos $filtro = null): CatalogoPromocionesDatos;
}
