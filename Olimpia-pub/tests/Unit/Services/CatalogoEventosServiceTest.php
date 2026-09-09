<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\EventoRepositoryInterface;
use App\DTOs\Dashboard\EventoDetalleDatos;
use App\DTOs\Dashboard\EventoTarjetaDatos;
use App\DTOs\Dashboard\FiltroRangoFechasDatos;
use App\Enums\EstadoEvento;
use App\Exceptions\Evento\EventoNoEncontradoException;
use App\Models\Evento;
use App\Services\CatalogoEventosService;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class CatalogoEventosServiceTest extends TestCase
{
    private EventoRepositoryInterface&MockInterface $repositorio;

    private CatalogoEventosService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositorio = Mockery::mock(EventoRepositoryInterface::class);
        $this->service = new CatalogoEventosService($this->repositorio);
    }

    public function test_catalogo_vacio_si_no_hay_eventos(): void
    {
        $this->repositorio->shouldReceive('listar')
            ->once()
            ->with(Mockery::type(FiltroRangoFechasDatos::class))
            ->andReturn(collect());

        $catalogo = $this->service->obtenerCatalogo();

        $this->assertFalse($catalogo->tieneEventos());
        $this->assertSame([], $catalogo->enOrden());
    }

    public function test_convierte_los_eventos_en_tarjetas(): void
    {
        $evento = new Evento([
            'nombre' => 'Trivia hincha',
            'descripcion' => 'Preguntas de fútbol',
            'fecha' => '2026-09-12',
            'hora' => '20:00',
            'estado' => EstadoEvento::Programado,
        ]);
        $evento->id_evento = 7;

        $filtro = new FiltroRangoFechasDatos(null, null);

        $this->repositorio->shouldReceive('listar')
            ->once()
            ->with($filtro)
            ->andReturn(Collection::make([$evento]));

        $catalogo = $this->service->obtenerCatalogo($filtro);
        $tarjeta = $catalogo->enOrden()[0];

        $this->assertTrue($catalogo->tieneEventos());
        $this->assertInstanceOf(EventoTarjetaDatos::class, $tarjeta);
        $this->assertSame(7, $tarjeta->id);
        $this->assertSame('Trivia hincha', $tarjeta->nombre);
        $this->assertSame('Preguntas de fútbol', $tarjeta->detalle());
    }

    public function test_detalle_devuelve_la_ficha_del_evento(): void
    {
        $evento = new Evento([
            'nombre' => 'DJ en vivo',
            'descripcion' => 'Sesión electrónica',
            'fecha' => '2026-09-15',
            'hora' => '22:00',
            'estado' => EstadoEvento::Programado,
        ]);
        $evento->id_evento = 3;

        $this->repositorio->shouldReceive('findById')
            ->once()
            ->with(3)
            ->andReturn($evento);

        $detalle = $this->service->obtenerDetalle(3);

        $this->assertInstanceOf(EventoDetalleDatos::class, $detalle);
        $this->assertSame(3, $detalle->tarjeta->id);
        $this->assertSame('DJ en vivo', $detalle->tarjeta->nombre);
        $this->assertSame('Programado', $detalle->estadoEtiqueta);
    }

    public function test_detalle_falla_si_el_evento_no_existe(): void
    {
        $this->repositorio->shouldReceive('findById')
            ->once()
            ->with(99)
            ->andReturn(null);

        $this->expectException(EventoNoEncontradoException::class);

        $this->service->obtenerDetalle(99);
    }
}
