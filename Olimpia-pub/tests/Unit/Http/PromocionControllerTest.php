<?php

namespace Tests\Unit\Http;

use App\Contracts\Services\CatalogoPromocionesServiceInterface;
use App\Contracts\Services\GestionPromocionesServiceInterface;
use App\DTOs\Dashboard\CatalogoPromocionesDatos;
use App\DTOs\Dashboard\FiltroPromocionesDatos;
use App\Http\Controllers\Dashboard\PromocionController;
use App\Http\Requests\ConsultarCatalogoPromocionesRequest;
use Mockery;
use Tests\TestCase;

class PromocionControllerTest extends TestCase
{
    public function test_mostrar_envia_el_catalogo_a_la_vista(): void
    {
        $catalogo = new CatalogoPromocionesDatos([]);
        $catalogoServicio = Mockery::mock(CatalogoPromocionesServiceInterface::class);
        $catalogoServicio->shouldReceive('obtenerCatalogo')
            ->once()
            ->with(Mockery::type(FiltroPromocionesDatos::class))
            ->andReturn($catalogo);

        $gestion = Mockery::mock(GestionPromocionesServiceInterface::class);
        $gestion->shouldReceive('listar')->once()->andReturn([]);

        $request = ConsultarCatalogoPromocionesRequest::create('/dashboard/promociones', 'GET');
        $controlador = new PromocionController($catalogoServicio, $gestion);
        $vista = $controlador->mostrar($request);

        $this->assertSame('dashboard.promociones', $vista->name());
        $this->assertSame($catalogo, $vista['catalogo']);
        $this->assertFalse($vista['abrirModal']);
        $this->assertNull($vista['promocionEditar']);
    }
}
