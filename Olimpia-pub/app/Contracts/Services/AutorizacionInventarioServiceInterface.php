<?php

namespace App\Contracts\Services;

use App\Models\Usuario;

interface AutorizacionInventarioServiceInterface
{
    /**
     * Indica si el usuario puede ver y gestionar el inventario.
     */
    public function puedeAcceder(?Usuario $usuario): bool;
}
