<?php

namespace App\Services;

use App\Contracts\Services\AutorizacionInventarioServiceInterface;
use App\Enums\RolUsuario;
use App\Models\Usuario;

class AutorizacionInventarioService implements AutorizacionInventarioServiceInterface
{
    /**
     * Solo empleados y administradores acceden al inventario.
     */
    public function puedeAcceder(?Usuario $usuario): bool
    {
        if ($usuario === null) {
            return false;
        }

        $usuario->loadMissing('rol');
        $rol = RolUsuario::tryFrom((string) $usuario->rol?->nombre_rol);

        return $rol?->puedeAccederInventario() ?? false;
    }
}
