<?php

namespace App\Contracts\Services;

interface EnlacePedidoMesaInterface
{
    /**
     * URL absoluta que el QR debe abrir para pedir en esa mesa.
     */
    public function url(string $codigo): string;
}
