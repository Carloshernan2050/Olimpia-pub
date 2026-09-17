<?php

namespace App\Contracts\Services;

use App\DTOs\Dashboard\CatalogoMesasDatos;
use App\DTOs\Dashboard\FiltroMesasDatos;

interface CatalogoMesasServiceInterface
{
    /**
     * Arma el tablero de mesas con QR y pedido activo.
     */
    public function obtenerCatalogo(?FiltroMesasDatos $filtro = null): CatalogoMesasDatos;
}
