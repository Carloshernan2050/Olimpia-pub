<?php

namespace Tests\Unit\Enums;

use App\Enums\PosicionInicio;
use App\Enums\TipoBloqueInicio;
use Tests\TestCase;

class PosicionInicioTest extends TestCase
{
    public function test_la_grilla_tiene_seis_posiciones_en_orden(): void
    {
        $orden = PosicionInicio::enOrdenDeGrilla();

        $this->assertCount(6, $orden);
        $this->assertSame(PosicionInicio::SuperiorIzquierda, $orden[0]);
        $this->assertSame(PosicionInicio::InferiorDerecha, $orden[5]);
    }

    public function test_cada_posicion_declara_su_tipo_de_bloque(): void
    {
        $this->assertSame(TipoBloqueInicio::Texto, PosicionInicio::SuperiorIzquierda->tipo());
        $this->assertSame(TipoBloqueInicio::Video, PosicionInicio::SuperiorCentro->tipo());
        $this->assertSame(TipoBloqueInicio::Texto, PosicionInicio::SuperiorDerecha->tipo());
        $this->assertSame(TipoBloqueInicio::Imagen, PosicionInicio::InferiorIzquierda->tipo());
        $this->assertSame(TipoBloqueInicio::Texto, PosicionInicio::InferiorCentro->tipo());
        $this->assertSame(TipoBloqueInicio::Imagen, PosicionInicio::InferiorDerecha->tipo());
    }
}
