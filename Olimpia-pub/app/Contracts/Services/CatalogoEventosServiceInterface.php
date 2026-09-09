<?php

namespace App\Contracts\Services;

use App\DTOs\Dashboard\CatalogoEventosDatos;
use App\DTOs\Dashboard\EventoDetalleDatos;
use App\DTOs\Dashboard\FiltroRangoFechasDatos;

interface CatalogoEventosServiceInterface
{
    /**
     * Arma el catálogo de eventos según el filtro.
     */
    public function obtenerCatalogo(?FiltroRangoFechasDatos $filtro = null): CatalogoEventosDatos;

    /**
     * Devuelve la ficha de un evento o falla si no existe.
     */
    public function obtenerDetalle(int $id): EventoDetalleDatos;
}
