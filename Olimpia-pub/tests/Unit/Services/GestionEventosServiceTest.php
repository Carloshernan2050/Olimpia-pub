<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\EventoRepositoryInterface;
use App\Contracts\Services\AlmacenamientoImagenPublicaInterface;
use App\DTOs\Dashboard\EventoGestionDatos;
use App\DTOs\Dashboard\GuardarEventoDatos;
use App\Enums\EstadoEvento;
use App\Exceptions\Evento\EventoNoEncontradoException;
use App\Models\Evento;
use App\Services\GestionEventosService;
use Illuminate\Http\UploadedFile;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class GestionEventosServiceTest extends TestCase
{
    private EventoRepositoryInterface&MockInterface $repositorio;

    private AlmacenamientoImagenPublicaInterface&MockInterface $imagenes;

    private GestionEventosService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositorio = Mockery::mock(EventoRepositoryInterface::class);
        $this->imagenes = Mockery::mock(AlmacenamientoImagenPublicaInterface::class);
        $this->service = new GestionEventosService($this->repositorio, $this->imagenes);
    }

    public function test_crear_persiste_y_devuelve_el_dto(): void
    {
        $datos = GuardarEventoDatos::fromValidated([
            'nombre' => 'Karaoke',
            'descripcion' => 'Micrófono',
            'fecha' => '2026-09-10',
            'hora' => '21:00',
            'estado' => 'programado',
        ]);
        $modelo = $this->modelo(['nombre' => 'Karaoke', 'descripcion' => 'Micrófono']);

        $this->repositorio->shouldReceive('create')
            ->once()
            ->with($datos->paraCrear(4))
            ->andReturn($modelo);

        $creado = $this->service->crear($datos, 4);

        $this->assertInstanceOf(EventoGestionDatos::class, $creado);
        $this->assertSame('Karaoke', $creado->nombre);
        $this->assertSame(9, $creado->id);
    }

    public function test_actualizar_reemplaza_la_imagen_anterior(): void
    {
        $datos = GuardarEventoDatos::fromValidated([
            'nombre' => 'Karaoke',
            'fecha' => '2026-09-10',
            'hora' => '21:00',
        ]);
        $actual = $this->modelo(['url_imagen' => 'eventos/vieja.jpg']);
        $archivo = UploadedFile::fake()->image('nueva.jpg');
        $actualizado = $this->modelo(['url_imagen' => 'eventos/nueva.jpg']);

        $this->repositorio->shouldReceive('findById')->once()->with(9)->andReturn($actual);
        $this->imagenes->shouldReceive('eliminar')->once()->with('eventos/vieja.jpg');
        $this->imagenes->shouldReceive('guardar')->once()->with($archivo)->andReturn('eventos/nueva.jpg');
        $this->repositorio->shouldReceive('update')
            ->once()
            ->with($actual, Mockery::on(fn (array $payload): bool => ($payload['url_imagen'] ?? null) === 'eventos/nueva.jpg'))
            ->andReturn($actualizado);

        $resultado = $this->service->actualizar(9, $datos, $archivo);

        $this->assertSame(9, $resultado->id);
        $this->assertSame('eventos/nueva.jpg', $resultado->urlImagen);
    }

    public function test_buscar_devuelve_null_si_no_existe(): void
    {
        $this->repositorio->shouldReceive('findById')->once()->with(99)->andReturn(null);

        $this->assertNull($this->service->buscar(99));
    }

    public function test_actualizar_falla_si_no_existe(): void
    {
        $this->repositorio->shouldReceive('findById')->once()->with(99)->andReturn(null);

        $this->expectException(EventoNoEncontradoException::class);

        $this->service->actualizar(99, GuardarEventoDatos::fromValidated([
            'nombre' => 'X',
            'fecha' => '2026-09-10',
            'hora' => '20:00',
        ]));
    }

    public function test_eliminar_borra_el_evento_y_la_imagen(): void
    {
        $modelo = $this->modelo(['url_imagen' => 'eventos/foto.jpg']);

        $this->repositorio->shouldReceive('findById')->once()->with(9)->andReturn($modelo);
        $this->imagenes->shouldReceive('eliminar')->once()->with('eventos/foto.jpg');
        $this->repositorio->shouldReceive('delete')->once()->with($modelo);

        $this->service->eliminar(9);
    }

    public function test_listar_mapea_todos_los_eventos(): void
    {
        $this->repositorio->shouldReceive('todas')
            ->once()
            ->andReturn(collect([$this->modelo()]));

        $listado = $this->service->listar();

        $this->assertCount(1, $listado);
        $this->assertSame('Karaoke', $listado[0]->nombre);
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function modelo(array $extra = []): Evento
    {
        $evento = new Evento([
            'nombre' => 'Karaoke',
            'descripcion' => 'Micrófono',
            'fecha' => '2026-09-10',
            'hora' => '21:00',
            'estado' => EstadoEvento::Programado,
            ...$extra,
        ]);
        $evento->id_evento = 9;

        return $evento;
    }
}
