<?php

namespace Tests\Unit\Services;

use App\Services\AlmacenamientoImagenPromocion;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Mockery;
use Tests\TestCase;

class AlmacenamientoImagenPromocionTest extends TestCase
{
    public function test_guardar_almacena_en_promociones(): void
    {
        $archivo = UploadedFile::fake()->image('combo.jpg');
        $disco = Mockery::mock(Filesystem::class);
        $disco->shouldReceive('putFileAs')
            ->once()
            ->with('promociones', $archivo, Mockery::type('string'))
            ->andReturn('promociones/abc.jpg');

        $ruta = (new AlmacenamientoImagenPromocion($disco))->guardar($archivo);

        $this->assertSame('promociones/abc.jpg', $ruta);
    }

    public function test_eliminar_ignora_rutas_vacias_y_absolutas(): void
    {
        $disco = Mockery::mock(Filesystem::class);
        $disco->shouldNotReceive('delete');

        $almacen = new AlmacenamientoImagenPromocion($disco);
        $almacen->eliminar(null);
        $almacen->eliminar('https://olimpia.test/combo.jpg');
    }

    public function test_eliminar_borra_una_ruta_local(): void
    {
        $disco = Mockery::mock(Filesystem::class);
        $disco->shouldReceive('delete')->once()->with('promociones/abc.jpg');

        (new AlmacenamientoImagenPromocion($disco))->eliminar('promociones/abc.jpg');
    }
}
