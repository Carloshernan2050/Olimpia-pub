<?php

namespace Tests\Unit\Http;

use App\Contracts\Services\ContenidoInicioServiceInterface;
use App\DTOs\Dashboard\BloqueInicioDatos;
use App\DTOs\Dashboard\PortadaInicioDatos;
use App\Enums\PosicionInicio;
use App\Http\Controllers\Dashboard\DashboardController;
use Mockery;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    public function test_mostrar_envia_la_portada_a_la_vista(): void
    {
        $portada = new PortadaInicioDatos([
            BloqueInicioDatos::vacio(PosicionInicio::SuperiorIzquierda),
        ]);
        $servicio = Mockery::mock(ContenidoInicioServiceInterface::class);
        $servicio->shouldReceive('obtenerPortada')->once()->andReturn($portada);

        $controlador = new DashboardController($servicio);
        $vista = $controlador->mostrar();

        $this->assertSame('dashboard.inicio', $vista->name());
        $this->assertSame($portada, $vista['portada']);
    }
}
