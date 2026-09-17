<?php

namespace Tests\Unit\Services;

use App\Services\AlmacenamientoImagenProducto;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Mockery;
use Tests\TestCase;

class AlmacenamientoImagenProductoTest extends TestCase
{
    public function test_guardar_almacena_en_productos(): void
    {
        $archivo = UploadedFile::fake()->image('limonada.jpg');
        $disco = Mockery::mock(Filesystem::class);
        $disco->shouldReceive('putFileAs')
            ->once()
            ->with('productos', $archivo, Mockery::type('string'))
            ->andReturn('productos/abc.jpg');

        $ruta = (new AlmacenamientoImagenProducto($disco))->guardar($archivo);

        $this->assertSame('productos/abc.jpg', $ruta);
    }

    public function test_eliminar_ignora_rutas_vacias_y_absolutas(): void
    {
        $disco = Mockery::mock(Filesystem::class);
        $disco->shouldNotReceive('delete');

        $almacen = new AlmacenamientoImagenProducto($disco);
        $almacen->eliminar(null);
        $almacen->eliminar('https://olimpia.test/limonada.jpg');
    }

    public function test_eliminar_borra_una_ruta_local(): void
    {
        $disco = Mockery::mock(Filesystem::class);
        $disco->shouldReceive('delete')->once()->with('productos/abc.jpg');

        (new AlmacenamientoImagenProducto($disco))->eliminar('productos/abc.jpg');
    }
}
