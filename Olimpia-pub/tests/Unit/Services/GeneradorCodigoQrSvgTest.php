<?php

namespace Tests\Unit\Services;

use App\Services\GeneradorCodigoQrSvg;
use Tests\TestCase;

class GeneradorCodigoQrSvgTest extends TestCase
{
    public function test_convierte_el_codigo_en_svg(): void
    {
        $svg = (new GeneradorCodigoQrSvg)->svg('OLIMPIA-MESA-01');

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringNotContainsString('<?xml', $svg);
    }
}
