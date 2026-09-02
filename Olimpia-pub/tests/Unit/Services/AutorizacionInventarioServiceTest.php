<?php

namespace Tests\Unit\Services;

use App\Models\Rol;
use App\Models\Usuario;
use App\Services\AutorizacionInventarioService;
use Tests\TestCase;

class AutorizacionInventarioServiceTest extends TestCase
{
    public function test_empleados_y_administradores_pueden_acceder(): void
    {
        $service = new AutorizacionInventarioService;

        $this->assertTrue($service->puedeAcceder($this->usuarioConRol('empleado')));
        $this->assertTrue($service->puedeAcceder($this->usuarioConRol('administrador')));
        $this->assertTrue($service->puedeAcceder($this->usuarioConRol('superadministrador')));
        $this->assertFalse($service->puedeAcceder($this->usuarioConRol('cliente')));
        $this->assertFalse($service->puedeAcceder(null));
    }

    private function usuarioConRol(string $nombreRol): Usuario
    {
        $usuario = new Usuario([
            'primer_nombre' => 'Ana',
            'primer_apellido' => 'Perez',
            'correo' => 'ana@olimpia.com',
        ]);
        $usuario->setRelation('rol', new Rol(['nombre_rol' => $nombreRol]));

        return $usuario;
    }
}
