<?php

namespace App\Contracts\Services;

use App\DTOs\Dashboard\CatalogoMenuDatos;
use App\DTOs\Dashboard\FiltroMenuDatos;

interface CatalogoMenuServiceInterface
{
    /**
     * Arma el menú a partir de los productos del inventario.
     */
    public function obtenerCatalogo(?FiltroMenuDatos $filtro = null): CatalogoMenuDatos;
}
