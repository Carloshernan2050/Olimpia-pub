<?php

namespace Tests\Unit\Services;

use App\Services\EnlacePedidoMesa;
use Illuminate\Contracts\Routing\UrlGenerator;
use Tests\TestCase;

class EnlacePedidoMesaTest extends TestCase
{
    public function test_arma_la_url_absoluta_del_menu_de_la_mesa(): void
    {
        $enlace = new EnlacePedidoMesa($this->app->make(UrlGenerator::class));

        $url = $enlace->url('OLIMPIA-MESA-01');

        $this->assertSame(route('mesa.menu', ['codigo' => 'OLIMPIA-MESA-01'], true), $url);
        $this->assertStringContainsString('/mesa/OLIMPIA-MESA-01', $url);
    }
}
