<?php

namespace Tests\Unit\Support;

use App\Support\Dashboard\RutasDashboard;
use Tests\TestCase;

class RutasDashboardTest extends TestCase
{
    public function test_devuelve_la_ruta_de_un_evento_por_id(): void
    {
        $this->assertSame('/dashboard/eventos/{evento}', RutasDashboard::eventoPorId());
    }
}
