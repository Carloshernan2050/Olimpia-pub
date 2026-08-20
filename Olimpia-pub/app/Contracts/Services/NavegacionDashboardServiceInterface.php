<?php

namespace App\Contracts\Services;

use App\DTOs\Dashboard\AccionCabeceraDatos;
use App\DTOs\Dashboard\ItemNavegacionDatos;

interface NavegacionDashboardServiceInterface
{
    /**
     * Ítems de la barra secundaria del dashboard.
     *
     * @return list<ItemNavegacionDatos>
     */
    public function items(): array;

    /**
     * Acciones del header del dashboard.
     *
     * @return list<AccionCabeceraDatos>
     */
    public function accionesCabecera(): array;

    /**
     * Clave de la sección activa según la ruta actual.
     */
    public function seccionActiva(): string;
}
