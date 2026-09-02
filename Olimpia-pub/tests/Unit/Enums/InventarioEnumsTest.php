<?php

namespace Tests\Unit\Enums;

use App\Enums\EstadoStockInventario;
use App\Enums\TipoMovimientoInventario;
use Tests\TestCase;

class InventarioEnumsTest extends TestCase
{
    public function test_clasifica_el_stock_segun_el_umbral(): void
    {
        $this->assertSame(EstadoStockInventario::Agotado, EstadoStockInventario::fromStock(0));
        $this->assertSame(EstadoStockInventario::Agotado, EstadoStockInventario::fromStock(-1));
        $this->assertSame(EstadoStockInventario::Bajo, EstadoStockInventario::fromStock(1));
        $this->assertSame(EstadoStockInventario::Bajo, EstadoStockInventario::fromStock(10));
        $this->assertSame(EstadoStockInventario::Disponible, EstadoStockInventario::fromStock(11));
    }

    public function test_expone_etiquetas_y_valores(): void
    {
        $this->assertSame('Stock bajo', EstadoStockInventario::Bajo->etiqueta());
        $this->assertSame('Entrada', TipoMovimientoInventario::Entrada->etiqueta());
        $this->assertSame(['disponible', 'bajo', 'agotado'], EstadoStockInventario::valores());
        $this->assertSame(['entrada', 'salida'], TipoMovimientoInventario::valores());
    }
}
