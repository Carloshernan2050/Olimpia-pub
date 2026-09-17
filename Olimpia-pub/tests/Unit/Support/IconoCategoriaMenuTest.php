<?php

namespace Tests\Unit\Support;

use App\Support\Dashboard\IconoCategoriaMenu;
use Tests\TestCase;

class IconoCategoriaMenuTest extends TestCase
{
    public function test_asigna_icono_y_peso_segun_la_categoria(): void
    {
        $iconos = new IconoCategoriaMenu;

        $this->assertSame('estrella', $iconos->para('Comidas'));
        $this->assertSame('taza', $iconos->para('Bebidas'));
        $this->assertSame('pan', $iconos->para('Postres'));
        $this->assertSame('caja', $iconos->para('Snacks'));
        $this->assertSame(0, $iconos->peso('Comidas'));
        $this->assertSame(1, $iconos->peso('Bebidas'));
        $this->assertSame(2, $iconos->peso('Postres'));
        $this->assertSame(100, $iconos->peso('Snacks'));
    }
}
