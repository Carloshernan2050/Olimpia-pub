<?php

namespace Tests\Unit\View;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class AvisoFlashTest extends TestCase
{
    public function test_muestra_el_aviso_de_exito_con_cierre(): void
    {
        session()->flash('exito', 'Sesión iniciada correctamente.');

        $html = Blade::render('<x-aviso-flash />');

        $this->assertStringContainsString('data-aviso', $html);
        $this->assertStringContainsString('data-aviso-ms="4000"', $html);
        $this->assertStringContainsString('data-cerrar-aviso', $html);
        $this->assertStringContainsString('Sesión iniciada correctamente.', $html);
        $this->assertStringContainsString('aviso-flash-exito', $html);
    }

    public function test_muestra_el_aviso_de_error_con_cierre(): void
    {
        session()->flash('error', 'No se pudo guardar la promoción.');

        $html = Blade::render('<x-aviso-flash />');

        $this->assertStringContainsString('data-aviso', $html);
        $this->assertStringContainsString('No se pudo guardar la promoción.', $html);
        $this->assertStringContainsString('aviso-flash-error', $html);
    }

    public function test_no_renderiza_nada_sin_mensajes_flash(): void
    {
        $html = Blade::render('<x-aviso-flash />');

        $this->assertSame('', trim($html));
    }
}
