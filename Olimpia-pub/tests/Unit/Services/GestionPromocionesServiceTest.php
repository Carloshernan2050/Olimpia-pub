<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\PromocionRepositoryInterface;
use App\Contracts\Services\AlmacenamientoImagenPromocionInterface;
use App\DTOs\Dashboard\GuardarPromocionDatos;
use App\DTOs\Dashboard\PromocionGestionDatos;
use App\Exceptions\Promocion\PromocionNoEncontradaException;
use App\Models\Promocion;
use App\Services\GestionPromocionesService;
use Illuminate\Http\UploadedFile;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class GestionPromocionesServiceTest extends TestCase
{
    private PromocionRepositoryInterface&MockInterface $repositorio;

    private AlmacenamientoImagenPromocionInterface&MockInterface $imagenes;

    private GestionPromocionesService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositorio = Mockery::mock(PromocionRepositoryInterface::class);
        $this->imagenes = Mockery::mock(AlmacenamientoImagenPromocionInterface::class);
        $this->service = new GestionPromocionesService($this->repositorio, $this->imagenes);
    }

    public function test_crear_persiste_y_devuelve_el_dto(): void
    {
        $datos = GuardarPromocionDatos::fromValidated([
            'nombre' => 'Combo',
            'descripcion' => 'Picada',
            'descuento' => '15',
            'fecha_inicio' => '2026-08-01',
            'fecha_fin' => '2026-08-31',
            'estado' => 'activa',
        ]);
        $modelo = $this->modelo(['nombre' => 'Combo', 'descripcion' => 'Picada']);

        $this->repositorio->shouldReceive('create')
            ->once()
            ->with($datos->paraCrear(4))
            ->andReturn($modelo);

        $creada = $this->service->crear($datos, 4);

        $this->assertInstanceOf(PromocionGestionDatos::class, $creada);
        $this->assertSame('Combo', $creada->nombre);
        $this->assertSame(9, $creada->id);
    }

    public function test_crear_guarda_la_imagen_si_se_envia(): void
    {
        $datos = GuardarPromocionDatos::fromValidated([
            'nombre' => 'Combo',
            'descuento' => '15',
            'fecha_inicio' => '2026-08-01',
            'fecha_fin' => '2026-08-31',
        ]);
        $archivo = UploadedFile::fake()->image('combo.jpg');
        $modelo = $this->modelo(['url_imagen' => 'promociones/combo.jpg']);

        $this->imagenes->shouldReceive('guardar')->once()->with($archivo)->andReturn('promociones/combo.jpg');
        $this->repositorio->shouldReceive('create')
            ->once()
            ->with(Mockery::on(fn (array $payload): bool => ($payload['url_imagen'] ?? null) === 'promociones/combo.jpg'))
            ->andReturn($modelo);

        $creada = $this->service->crear($datos, 4, $archivo);

        $this->assertSame('promociones/combo.jpg', $creada->urlImagen);
        $this->assertTrue($creada->tieneImagen());
    }

    public function test_actualizar_falla_si_no_existe(): void
    {
        $this->repositorio->shouldReceive('findById')->once()->with(99)->andReturn(null);

        $this->expectException(PromocionNoEncontradaException::class);

        $this->service->actualizar(99, GuardarPromocionDatos::fromValidated([
            'nombre' => 'X',
            'descuento' => '1',
            'fecha_inicio' => '2026-08-01',
            'fecha_fin' => '2026-08-02',
        ]));
    }

    public function test_eliminar_borra_la_imagen(): void
    {
        $modelo = $this->modelo(['url_imagen' => 'promociones/combo.jpg']);

        $this->repositorio->shouldReceive('findById')->once()->with(9)->andReturn($modelo);
        $this->imagenes->shouldReceive('eliminar')->once()->with('promociones/combo.jpg');
        $this->repositorio->shouldReceive('delete')->once()->with($modelo);

        $this->service->eliminar(9);
    }

    public function test_listar_mapea_todas_las_promociones(): void
    {
        $this->repositorio->shouldReceive('todas')
            ->once()
            ->andReturn(collect([$this->modelo()]));

        $listado = $this->service->listar();

        $this->assertCount(1, $listado);
        $this->assertSame('Combo', $listado[0]->nombre);
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function modelo(array $extra = []): Promocion
    {
        $promocion = new Promocion([
            'nombre' => 'Combo',
            'descripcion' => 'Picada',
            'descuento' => '15.00',
            'fecha_inicio' => '2026-08-01',
            'fecha_fin' => '2026-08-31',
            'estado' => 'activa',
            ...$extra,
        ]);
        $promocion->id_promocion = 9;

        return $promocion;
    }
}
