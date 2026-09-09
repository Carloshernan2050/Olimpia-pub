<?php

namespace Tests\Unit\Enums;

use App\Enums\EstadoEvento;
use PHPUnit\Framework\TestCase;

class EstadoEventoTest extends TestCase
{
    public function test_etiqueta_de_cada_estado(): void
    {
        $this->assertSame('Programado', EstadoEvento::Programado->etiqueta());
        $this->assertSame('Cancelado', EstadoEvento::Cancelado->etiqueta());
        $this->assertSame('Finalizado', EstadoEvento::Finalizado->etiqueta());
    }

    public function test_desde_valor_desconocido_queda_programado(): void
    {
        $this->assertSame(EstadoEvento::Programado, EstadoEvento::desdeValor(null));
        $this->assertSame(EstadoEvento::Programado, EstadoEvento::desdeValor('otro'));
        $this->assertSame(EstadoEvento::Cancelado, EstadoEvento::desdeValor('cancelado'));
    }

    public function test_valores_incluye_los_estados_del_formulario(): void
    {
        $this->assertSame(['programado', 'cancelado', 'finalizado'], EstadoEvento::valores());
    }
}
