<?php

namespace App\Enums;

enum RolUsuario: string
{
    case Cliente = 'cliente';
    case Empleado = 'empleado';
    case Administrador = 'administrador';
    case Superadministrador = 'superadministrador';

    /**
     * El inventario es interno: empleados y administradores.
     */
    public function puedeAccederInventario(): bool
    {
        return match ($this) {
            self::Empleado,
            self::Administrador,
            self::Superadministrador => true,
            self::Cliente => false,
        };
    }
}
