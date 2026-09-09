<?php

namespace Tests\Unit\Http;

use App\Contracts\Services\CatalogoEventosServiceInterface;
use App\Contracts\Services\GestionEventosServiceInterface;
use App\DTOs\Dashboard\CatalogoEventosDatos;
use App\DTOs\Dashboard\FiltroRangoFechasDatos;
use App\Http\Controllers\Dashboard\EventoController;
use App\Http\Requests\ConsultarCatalogoEventosRequest;
use Mockery;
use Tests\TestCase;

class EventoControllerTest extends TestCase
{
    public function test_mostrar_envia_el_catalogo_a_la_vista(): void
    {
        $catalogo = new CatalogoEventosDatos([]);
        $catalogoServicio = Mockery::mock(CatalogoEventosServiceInterface::class);
        $catalogoServicio->shouldReceive('obtenerCatalogo')
            ->once()
            ->with(Mockery::type(FiltroRangoFechasDatos::class))
            ->andReturn($catalogo);

        $gestion = Mockery::mock(GestionEventosServiceInterface::class);
        $gestion->shouldReceive('listar')->once()->andReturn([]);

        $request = ConsultarCatalogoEventosRequest::create('/dashboard/eventos', 'GET');
        $controlador = new EventoController($catalogoServicio, $gestion);
        $vista = $controlador->mostrar($request);

        $this->assertSame('dashboard.eventos', $vista->name());
        $this->assertSame($catalogo, $vista['catalogo']);
        $this->assertFalse($vista['abrirModal']);
        $this->assertNull($vista['eventoEditar']);
        $this->assertNull($vista['eventoVer']);
        $this->assertInstanceOf(FiltroRangoFechasDatos::class, $vista['filtro']);
    }

    public function test_detalle_redirige_al_catalogo_con_ver(): void
    {
        $catalogoServicio = Mockery::mock(CatalogoEventosServiceInterface::class);
        $gestion = Mockery::mock(GestionEventosServiceInterface::class);
        $controlador = new EventoController($catalogoServicio, $gestion);

        $respuesta = $controlador->detalle(4);

        $this->assertTrue($respuesta->isRedirect(route('eventos', ['ver' => 4])));
    }
}
