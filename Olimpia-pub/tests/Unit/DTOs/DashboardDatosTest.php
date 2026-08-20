<?php

namespace Tests\Unit\DTOs;

use App\DTOs\Dashboard\BloqueInicioDatos;
use App\DTOs\Dashboard\PortadaInicioDatos;
use App\Enums\PosicionInicio;
use App\Enums\TipoBloqueInicio;
use App\Models\ContenidoInicio;
use Tests\TestCase;

class DashboardDatosTest extends TestCase
{
    public function test_bloque_vacio_usa_el_tipo_de_la_posicion(): void
    {
        $bloque = BloqueInicioDatos::vacio(PosicionInicio::SuperiorCentro);

        $this->assertSame(TipoBloqueInicio::Video, $bloque->tipo);
        $this->assertFalse($bloque->tieneContenido());
        $this->assertNull($bloque->urlMediaPublica());
    }

    public function test_from_model_copia_titulo_cuerpo_y_media(): void
    {
        $contenido = new ContenidoInicio([
            'posicion' => PosicionInicio::SuperiorIzquierda,
            'tipo' => TipoBloqueInicio::Texto,
            'titulo' => 'Portada',
            'cuerpo' => 'Texto de home',
            'url_media' => '/media/inicio/portada.mp4',
        ]);

        $bloque = BloqueInicioDatos::fromModel($contenido);

        $this->assertTrue($bloque->tieneContenido());
        $this->assertSame('Portada', $bloque->titulo);
        $this->assertSame(asset('/media/inicio/portada.mp4'), $bloque->urlMediaPublica());
        $this->assertSame(asset('/media/inicio/portada.es.vtt'), $bloque->urlSubtitulosPublica());
        $this->assertSame(
            asset('/media/inicio/portada.descripcion.es.vtt'),
            $bloque->urlDescripcionPublica(),
        );
    }

    public function test_url_absoluta_se_deja_igual(): void
    {
        $bloque = new BloqueInicioDatos(
            PosicionInicio::SuperiorCentro,
            TipoBloqueInicio::Video,
            'Clip',
            null,
            'https://olimpia.test/video.mp4',
        );

        $this->assertSame('https://olimpia.test/video.mp4', $bloque->urlMediaPublica());
        $this->assertSame('https://olimpia.test/video.es.vtt', $bloque->urlSubtitulosPublica());
        $this->assertTrue($bloque->tieneContenido());
    }

    public function test_portada_devuelve_los_bloques_en_el_mismo_orden(): void
    {
        $primero = BloqueInicioDatos::vacio(PosicionInicio::SuperiorIzquierda);
        $portada = new PortadaInicioDatos([$primero]);

        $this->assertSame([$primero], $portada->bloquesEnOrden());
    }

    public function test_imagen_svg_no_genera_pistas_de_video(): void
    {
        $bloque = new BloqueInicioDatos(
            PosicionInicio::InferiorIzquierda,
            TipoBloqueInicio::Imagen,
            'Zona',
            null,
            '/media/inicio/zona-pantallas.svg',
        );

        $this->assertNull($bloque->urlSubtitulosPublica());
        $this->assertNull($bloque->urlDescripcionPublica());
        $this->assertTrue($bloque->tieneContenido());
    }

    public function test_http_tambien_se_trata_como_url_absoluta(): void
    {
        $bloque = new BloqueInicioDatos(
            PosicionInicio::SuperiorCentro,
            TipoBloqueInicio::Video,
            'Clip',
            null,
            'http://olimpia.test/clip.MP4',
        );

        $this->assertSame('http://olimpia.test/clip.MP4', $bloque->urlMediaPublica());
        $this->assertSame('http://olimpia.test/clip.descripcion.es.vtt', $bloque->urlDescripcionPublica());
    }

    public function test_tiene_contenido_si_solo_hay_cuerpo(): void
    {
        $bloque = new BloqueInicioDatos(
            PosicionInicio::InferiorCentro,
            TipoBloqueInicio::Texto,
            null,
            'Solo texto',
            null,
        );

        $this->assertTrue($bloque->tieneContenido());
        $this->assertNull($bloque->urlMediaPublica());
        $this->assertNull($bloque->urlSubtitulosPublica());
    }
}
