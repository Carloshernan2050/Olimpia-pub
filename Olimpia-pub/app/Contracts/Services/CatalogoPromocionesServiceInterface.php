<?php

namespace App\Contracts\Services;

use App\DTOs\Dashboard\CatalogoPromocionesDatos;
use App\DTOs\Dashboard\FiltroPromocionesDatos;

interface CatalogoPromocionesServiceInterface
{
    /**
     * Arma el catálogo de promociones vigentes según el filtro.
     */
    public function obtenerCatalogo(?FiltroPromocionesDatos $filtro = null): CatalogoPromocionesDatos;
}
