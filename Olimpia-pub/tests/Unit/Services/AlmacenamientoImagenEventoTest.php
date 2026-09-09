<?php

namespace Tests\Unit\Services;

use App\Services\AlmacenamientoImagenEvento;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Mockery;
use Tests\TestCase;

class AlmacenamientoImagenEventoTest extends TestCase
{
    public function test_guardar_almacena_en_eventos(): void
    {
        $archivo = UploadedFile::fake()->image('noche.jpg');
        $disco = Mockery::mock(Filesystem::class);
        $disco->shouldReceive('putFileAs')
            ->once()
            ->with('eventos', $archivo, Mockery::type('string'))
            ->andReturn('eventos/abc.jpg');

        $ruta = (new AlmacenamientoImagenEvento($disco))->guardar($archivo);

        $this->assertSame('eventos/abc.jpg', $ruta);
    }

    public function test_eliminar_ignora_rutas_vacias_y_absolutas(): void
    {
        $disco = Mockery::mock(Filesystem::class);
        $disco->shouldNotReceive('delete');

        $almacen = new AlmacenamientoImagenEvento($disco);
        $almacen->eliminar(null);
        $almacen->eliminar('https://olimpia.test/noche.jpg');
    }

    public function test_eliminar_borra_una_ruta_local(): void
    {
        $disco = Mockery::mock(Filesystem::class);
        $disco->shouldReceive('delete')->once()->with('eventos/abc.jpg');

        (new AlmacenamientoImagenEvento($disco))->eliminar('eventos/abc.jpg');
    }
}
