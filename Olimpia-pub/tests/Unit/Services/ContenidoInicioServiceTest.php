<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\ContenidoInicioRepositoryInterface;
use App\DTOs\Dashboard\BloqueInicioDatos;
use App\Enums\PosicionInicio;
use App\Enums\TipoBloqueInicio;
use App\Models\ContenidoInicio;
use App\Services\ContenidoInicioService;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class ContenidoInicioServiceTest extends TestCase
{
    private ContenidoInicioRepositoryInterface&MockInterface $repositorio;

    private ContenidoInicioService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositorio = Mockery::mock(ContenidoInicioRepositoryInterface::class);
        $this->service = new ContenidoInicioService($this->repositorio);
    }

    public function test_completa_la_grilla_con_bloques_vacios_si_no_hay_contenido(): void
    {
        $this->repositorio->shouldReceive('activosPorPosicion')
            ->once()
            ->andReturn(collect());

        $portada = $this->service->obtenerPortada();
        $bloques = $portada->bloquesEnOrden();

        $this->assertCount(6, $bloques);
        $this->assertSame(PosicionInicio::SuperiorIzquierda, $bloques[0]->posicion);
        $this->assertSame(TipoBloqueInicio::Texto, $bloques[0]->tipo);
        $this->assertSame(TipoBloqueInicio::Video, $bloques[1]->tipo);
        $this->assertFalse($bloques[0]->tieneContenido());
    }

    public function test_usa_el_contenido_activo_de_cada_posicion(): void
    {
        $contenido = new ContenidoInicio([
            'posicion' => PosicionInicio::SuperiorIzquierda,
            'tipo' => TipoBloqueInicio::Texto,
            'titulo' => 'El partido se vive mejor en Olimpia',
            'cuerpo' => 'Texto de portada',
            'url_media' => null,
        ]);

        $this->repositorio->shouldReceive('activosPorPosicion')
            ->once()
            ->andReturn(Collection::make([
                PosicionInicio::SuperiorIzquierda->value => $contenido,
            ]));

        $portada = $this->service->obtenerPortada();
        $primero = $portada->bloquesEnOrden()[0];

        $this->assertInstanceOf(BloqueInicioDatos::class, $primero);
        $this->assertSame('El partido se vive mejor en Olimpia', $primero->titulo);
        $this->assertTrue($primero->tieneContenido());
        $this->assertNull($portada->bloquesEnOrden()[1]->titulo);
        $this->assertNull($portada->bloquesEnOrden()[1]->urlSubtitulosPublica());
    }
}
