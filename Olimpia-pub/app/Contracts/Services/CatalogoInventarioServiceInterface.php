<?php

namespace App\Contracts\Services;

use App\DTOs\Dashboard\CatalogoInventarioDatos;
use App\DTOs\Dashboard\FiltroInventarioDatos;

interface CatalogoInventarioServiceInterface
{
    /**
     * Arma el catálogo de inventario según el filtro.
     */
    public function obtenerCatalogo(?FiltroInventarioDatos $filtro = null): CatalogoInventarioDatos;
}
