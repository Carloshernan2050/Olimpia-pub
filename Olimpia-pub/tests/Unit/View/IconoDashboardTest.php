<?php

namespace Tests\Unit\View;

use App\Support\Dashboard\CatalogoIconos;
use App\View\Components\Dashboard\Icono;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class IconoDashboardTest extends TestCase
{
    public function test_renderiza_el_svg_del_catalogo(): void
    {
        $icono = new Icono($this->app->make(CatalogoIconos::class), 'inicio');

        $this->assertStringContainsString('<svg', $icono->svg());
        $this->assertSame('components.dashboard.icono', $icono->render()->name());
    }

    public function test_el_componente_blade_incluye_aria_label_si_hay_titulo(): void
    {
        $html = Blade::render('<x-dashboard.icono nombre="imagen" titulo="Imagen de inicio" />');

        $this->assertStringContainsString('aria-label="Imagen de inicio"', $html);
        $this->assertStringContainsString('<svg', $html);
    }

    public function test_sin_titulo_marca_el_icono_como_decorativo(): void
    {
        $html = Blade::render('<x-dashboard.icono nombre="inicio" />');

        $this->assertStringContainsString('aria-hidden="true"', $html);
        $this->assertStringNotContainsString('aria-label', $html);
    }
}
