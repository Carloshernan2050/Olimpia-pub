<?php

namespace App\Services;

use App\Contracts\Services\EnlacePedidoMesaInterface;
use Illuminate\Contracts\Routing\UrlGenerator;

class EnlacePedidoMesa implements EnlacePedidoMesaInterface
{
    /**
     * Inyecta el generador de URLs de la aplicación.
     */
    public function __construct(
        private readonly UrlGenerator $urls,
    ) {}

    /**
     * URL absoluta del menú público de la mesa.
     */
    public function url(string $codigo): string
    {
        return $this->urls->route('mesa.menu', ['codigo' => $codigo], true);
    }
}
