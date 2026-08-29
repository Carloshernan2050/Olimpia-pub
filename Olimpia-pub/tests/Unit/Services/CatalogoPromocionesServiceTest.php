<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\PromocionRepositoryInterface;
use App\DTOs\Dashboard\FiltroPromocionesDatos;
use App\DTOs\Dashboard\PromocionTarjetaDatos;
use App\Models\Promocion;
use App\Services\CatalogoPromocionesService;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class CatalogoPromocionesServiceTest extends TestCase
{
    private PromocionRepositoryInterface&MockInterface $repositorio;

    private CatalogoPromocionesService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositorio = Mockery::mock(PromocionRepositoryInterface::class);
        $this->service = new CatalogoPromocionesService($this->repositorio);
    }

    public function test_catalogo_vacio_si_no_hay_promociones(): void
    {
        $this->repositorio->shouldReceive('activas')
            ->once()
            ->with(Mockery::type(FiltroPromocionesDatos::class))
            ->andReturn(collect());

        $catalogo = $this->service->obtenerCatalogo();

        $this->assertFalse($catalogo->tienePromociones());
        $this->assertSame([], $catalogo->enOrden());
    }

    public function test_convierte_las_promociones_activas_en_tarjetas(): void
    {
        $promocion = new Promocion([
            'nombre' => '2x1',
            'descripcion' => 'En cervezas',
            'descuento' => 10,
        ]);
        $promocion->id_promocion = 7;

        $filtro = new FiltroPromocionesDatos(null, null);

        $this->repositorio->shouldReceive('activas')
            ->once()
            ->with($filtro)
            ->andReturn(Collection::make([$promocion]));

        $catalogo = $this->service->obtenerCatalogo($filtro);
        $tarjeta = $catalogo->enOrden()[0];

        $this->assertTrue($catalogo->tienePromociones());
        $this->assertInstanceOf(PromocionTarjetaDatos::class, $tarjeta);
        $this->assertSame(7, $tarjeta->id);
        $this->assertSame('2x1', $tarjeta->nombre);
        $this->assertSame('En cervezas', $tarjeta->detalle());
    }
}
