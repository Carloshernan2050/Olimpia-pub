<?php

namespace Tests\Unit\View;

use App\Contracts\Services\NavegacionDashboardServiceInterface;
use App\DTOs\Dashboard\AccionCabeceraDatos;
use App\DTOs\Dashboard\ItemNavegacionDatos;
use App\DTOs\Dashboard\PortadaInicioDatos;
use App\Models\Usuario;
use App\View\Composers\DashboardComposer;
use Mockery;
use Tests\TestCase;

class DashboardComposerTest extends TestCase
{
    public function test_sin_usuario_deja_el_nombre_vacio(): void
    {
        $composer = new DashboardComposer($this->navegacion());
        $vista = view('dashboard.inicio', ['portada' => new PortadaInicioDatos([])]);

        $composer->compose($vista);

        $this->assertSame('', $vista['nombreUsuario']);
        $this->assertSame('inicio', $vista['seccionActiva']);
        $this->assertNotEmpty($vista['itemsNavegacion']);
        $this->assertNotEmpty($vista['accionesCabecera']);
    }

    public function test_con_usuario_expone_el_primer_nombre(): void
    {
        $usuario = new Usuario([
            'primer_nombre' => 'Ana',
            'primer_apellido' => 'Perez',
            'correo' => 'ana@olimpia.com',
        ]);
        $this->be($usuario);

        $composer = new DashboardComposer($this->navegacion());
        $vista = view('dashboard.inicio', ['portada' => new PortadaInicioDatos([])]);

        $composer->compose($vista);

        $this->assertSame('Ana', $vista['nombreUsuario']);
    }

    private function navegacion(): NavegacionDashboardServiceInterface
    {
        $navegacion = Mockery::mock(NavegacionDashboardServiceInterface::class);
        $navegacion->shouldReceive('items')->andReturn([
            new ItemNavegacionDatos('inicio', 'Inicio', 'inicio', 'dashboard'),
        ]);
        $navegacion->shouldReceive('accionesCabecera')->andReturn([
            new AccionCabeceraDatos('perfil', 'Perfil', 'perfil', true),
        ]);
        $navegacion->shouldReceive('seccionActiva')->andReturn('inicio');

        return $navegacion;
    }
}
