<?php

namespace Tests\Unit\Http;

use App\Contracts\Services\CatalogoMesasServiceInterface;
use App\Contracts\Services\GestionGruposMesaServiceInterface;
use App\Contracts\Services\GestionMesasServiceInterface;
use App\Contracts\Services\GestionPedidosServiceInterface;
use App\DTOs\Dashboard\CatalogoMesasDatos;
use App\DTOs\Dashboard\FiltroMesasDatos;
use App\DTOs\Dashboard\TipoMesaDatos;
use App\Http\Controllers\Dashboard\MesaController;
use App\Http\Requests\ConsultarMesasRequest;
use Mockery;
use Tests\TestCase;

class MesaControllerTest extends TestCase
{
    public function test_mostrar_envia_el_catalogo_a_la_vista(): void
    {
        $catalogo = new CatalogoMesasDatos([], TipoMesaDatos::catalogo());
        $catalogoServicio = Mockery::mock(CatalogoMesasServiceInterface::class);
        $catalogoServicio->shouldReceive('obtenerCatalogo')
            ->once()
            ->with(Mockery::type(FiltroMesasDatos::class))
            ->andReturn($catalogo);

        $gestion = Mockery::mock(GestionMesasServiceInterface::class);
        $gestionGrupos = Mockery::mock(GestionGruposMesaServiceInterface::class);
        $gestionPedidos = Mockery::mock(GestionPedidosServiceInterface::class);
        $request = ConsultarMesasRequest::create('/dashboard/mesas', 'GET');
        $controlador = new MesaController($catalogoServicio, $gestion, $gestionGrupos, $gestionPedidos);
        $vista = $controlador->mostrar($request);

        $this->assertSame('dashboard.mesas', $vista->name());
        $this->assertSame($catalogo, $vista['catalogo']);
        $this->assertFalse($vista['abrirModal']);
        $this->assertFalse($vista['unir']);
        $this->assertFalse($vista['liberar']);
        $this->assertNull($vista['mesaVer']);
        $this->assertNull($vista['mesaEditar']);
        $this->assertNull($vista['pedidoVer']);
        $this->assertNull($vista['grupoVer']);
        $this->assertNull($vista['grupoEditar']);
        $this->assertNull($vista['pedidoGrupoVer']);
        $this->assertInstanceOf(FiltroMesasDatos::class, $vista['filtro']);
    }
}
