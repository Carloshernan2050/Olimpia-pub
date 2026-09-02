<?php

namespace Tests\Unit\Enums;

use App\Enums\RolUsuario;
use Tests\TestCase;

class RolUsuarioTest extends TestCase
{
    public function test_solo_empleados_y_administradores_acceden_al_inventario(): void
    {
        $this->assertFalse(RolUsuario::Cliente->puedeAccederInventario());
        $this->assertTrue(RolUsuario::Empleado->puedeAccederInventario());
        $this->assertTrue(RolUsuario::Administrador->puedeAccederInventario());
        $this->assertTrue(RolUsuario::Superadministrador->puedeAccederInventario());
    }
}
