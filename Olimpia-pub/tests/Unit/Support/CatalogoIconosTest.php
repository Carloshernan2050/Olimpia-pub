<?php

namespace Tests\Unit\Support;

use App\Support\Dashboard\CatalogoIconos;
use InvalidArgumentException;
use Tests\TestCase;

class CatalogoIconosTest extends TestCase
{
    public function test_renderiza_los_iconos_del_dashboard(): void
    {
        $catalogo = new CatalogoIconos;
        $nombres = [
            'balon', 'buscar', 'carrito', 'ubicacion', 'qr', 'ajustes', 'perfil',
            'inicio', 'etiqueta', 'megafono', 'herramienta', 'portapapeles',
            'pesa', 'grafica', 'estiramiento', 'historial', 'imagen',
        ];

        foreach ($nombres as $nombre) {
            $this->assertStringContainsString('<svg', $catalogo->render($nombre));
        }
    }

    public function test_rechaza_un_icono_desconocido(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new CatalogoIconos)->render('no-existe');
    }
}
